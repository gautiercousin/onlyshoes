CREATE OR REPLACE FUNCTION marquer_annonce_vendue()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE ANNONCE
    SET disponible = FALSE
    WHERE id_annonce = NEW.id_annonce;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_marquer_annonce_vendue
AFTER INSERT ON DETAILLER_COMMANDE
FOR EACH ROW
EXECUTE FUNCTION marquer_annonce_vendue();

CREATE OR REPLACE FUNCTION verif_paiement_valide()
RETURNS TRIGGER AS $$
DECLARE
    v_statut_paiement VARCHAR(20);
BEGIN
    SELECT statut INTO v_statut_paiement
    FROM PAIEMENT WHERE id_paiement = NEW.id_paiement;

    IF v_statut_paiement != 'valide' THEN
        RAISE EXCEPTION USING
            ERRCODE = 'SA013',
            MESSAGE = 'Le paiement doit être validé avant de créer une commande';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_verif_paiement_valide
BEFORE INSERT ON COMMANDE
FOR EACH ROW
EXECUTE FUNCTION verif_paiement_valide();

CREATE OR REPLACE FUNCTION verif_modification_commande()
RETURNS TRIGGER AS $$
BEGIN
    IF OLD.statut = 'livree' THEN
        RAISE EXCEPTION USING
            ERRCODE = 'SA003',
            MESSAGE = 'Impossible de modifier une commande déjà livrée';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_verif_modification_commande
BEFORE UPDATE ON COMMANDE
FOR EACH ROW
EXECUTE FUNCTION verif_modification_commande();

-- Trigger désactivé - la logique de suppression/anonymisation est gérée au niveau applicatif
-- Les commandes livrées ne seront pas supprimées, l'utilisateur sera anonymisé à la place

-- CREATE OR REPLACE FUNCTION verif_suppression_commande()
-- RETURNS TRIGGER AS $$
-- BEGIN
--     IF OLD.statut = 'livree' THEN
--         RAISE EXCEPTION USING
--             ERRCODE = 'SA004',
--             MESSAGE = 'Impossible de supprimer une commande déjà livrée';
--     END IF;
--     RETURN OLD;
-- END;
-- $$ LANGUAGE plpgsql;

-- CREATE TRIGGER trg_verif_suppression_commande
-- BEFORE DELETE ON COMMANDE
-- FOR EACH ROW
-- EXECUTE FUNCTION verif_suppression_commande();

CREATE OR REPLACE FUNCTION verif_vendeur_acheteur()
RETURNS TRIGGER AS $$
DECLARE
    v_id_acheteur UUID;
    v_id_vendeur UUID;
BEGIN
    -- Récupérer l'ID de l'acheteur depuis la commande
    SELECT id_utilisateur INTO v_id_acheteur
    FROM COMMANDE WHERE id_commande = NEW.id_commande;

    -- Récupérer l'ID du vendeur depuis l'annonce
    SELECT id_utilisateur_vendeur INTO v_id_vendeur
    FROM ANNONCE WHERE id_annonce = NEW.id_annonce;

    -- Vérifier que l'acheteur n'est pas le vendeur
    IF v_id_acheteur = v_id_vendeur THEN
        RAISE EXCEPTION USING
            ERRCODE = 'SA006',
            MESSAGE = 'Vous ne pouvez pas acheter votre propre annonce';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_verif_vendeur_acheteur
BEFORE INSERT ON DETAILLER_COMMANDE
FOR EACH ROW
EXECUTE FUNCTION verif_vendeur_acheteur();

CREATE OR REPLACE FUNCTION verif_review_autorisee()
RETURNS TRIGGER AS $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM COMMANDE c
        JOIN DETAILLER_COMMANDE dc ON c.id_commande = dc.id_commande
        JOIN ANNONCE a ON dc.id_annonce = a.id_annonce
        WHERE c.id_utilisateur = NEW.id_utilisateur_auteur
        AND a.id_utilisateur_vendeur = NEW.id_utilisateur_vendeur
        AND c.statut = 'livree'
    ) THEN
        RAISE EXCEPTION USING
            ERRCODE = 'SA010',
            MESSAGE = 'Vous devez avoir acheté chez ce vendeur pour laisser un avis';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_verif_review_autorisee
BEFORE INSERT ON REVIEW
FOR EACH ROW
EXECUTE FUNCTION verif_review_autorisee();


-- Nouvelle fonction de vérification d'intégrité pour SIGNALEMENT polymorphe
CREATE OR REPLACE FUNCTION verif_signalement_integrite()
RETURNS TRIGGER AS $$
DECLARE
    cible_exists BOOLEAN;
BEGIN
    -- Vérifier l'existence de la cible selon le type
    IF NEW.type = 'user' THEN
        SELECT EXISTS(SELECT 1 FROM UTILISATEUR WHERE id_utilisateur = NEW.id_utilisateur_cible) INTO cible_exists;
        IF NOT cible_exists THEN
            RAISE EXCEPTION USING ERRCODE = 'SA021', MESSAGE = 'Utilisateur cible inexistant';
        END IF;
        -- Empêcher l'auto-signalement pour les users
        IF NEW.id_utilisateur_auteur = NEW.id_utilisateur_cible THEN
            RAISE EXCEPTION USING ERRCODE = 'SA012', MESSAGE = 'Vous ne pouvez pas vous signaler vous-même';
        END IF;
    ELSIF NEW.type = 'annonce' THEN
        SELECT EXISTS(SELECT 1 FROM ANNONCE WHERE id_annonce = NEW.id_annonce_cible) INTO cible_exists;
        IF NOT cible_exists THEN
            RAISE EXCEPTION USING ERRCODE = 'SA022', MESSAGE = 'Annonce cible inexistante';
        END IF;
    ELSIF NEW.type = 'review' THEN
        SELECT EXISTS(SELECT 1 FROM REVIEW WHERE id_review = NEW.id_review_cible) INTO cible_exists;
        IF NOT cible_exists THEN
            RAISE EXCEPTION USING ERRCODE = 'SA023', MESSAGE = 'Review cible inexistante';
        END IF;
    ELSE
        RAISE EXCEPTION USING ERRCODE = 'SA024', MESSAGE = 'Type de signalement inconnu';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_verif_signalement_integrite
BEFORE INSERT ON SIGNALEMENT
FOR EACH ROW
EXECUTE FUNCTION verif_signalement_integrite();

CREATE OR REPLACE FUNCTION verif_password_hash()
RETURNS TRIGGER AS $$
BEGIN
    -- Ignorer la vérification pour les comptes bannis (anonymisés)
    IF NEW.status = 'bannis' AND NEW.email LIKE 'deleted_%@anonymized.local' THEN
        RETURN NEW;
    END IF;
    
    -- Merci Claude Code pour l'expression régulière de bcrypt
    IF NEW.mdp !~ '^\$2[aby]\$\d{2}\$.{53}$' THEN
        RAISE EXCEPTION USING
            ERRCODE = 'SA015',
            MESSAGE = 'Mot de passe non haché détecté. Le mot de passe doit être haché avec bcrypt avant insertion. SÉCURITÉ COMPROMISE: Changez immédiatement ce mot de passe.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_verif_password_hash
BEFORE INSERT OR UPDATE OF mdp ON UTILISATEUR
FOR EACH ROW
EXECUTE FUNCTION verif_password_hash();

CREATE OR REPLACE FUNCTION verif_admin_action()
RETURNS TRIGGER AS $$
DECLARE
    v_type_compte VARCHAR(20);
BEGIN
    SELECT type_compte INTO v_type_compte
    FROM UTILISATEUR WHERE id_utilisateur = NEW.id_utilisateur;

    IF v_type_compte != 'admin' THEN
        RAISE EXCEPTION USING
            ERRCODE = 'SA016',
            MESSAGE = 'Seuls les administrateurs peuvent effectuer des actions admin';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_verif_admin_action
BEFORE INSERT ON ADMIN_LOG
FOR EACH ROW
EXECUTE FUNCTION verif_admin_action();

CREATE OR REPLACE FUNCTION verif_email_format()
RETURNS TRIGGER AS $$
BEGIN
    -- Regex basique mais robuste pour email
    IF NEW.email !~ '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$' THEN
        RAISE EXCEPTION USING
            ERRCODE = 'SA017',
            MESSAGE = 'Format d''email invalide';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_verif_email_format
BEFORE INSERT OR UPDATE OF email ON UTILISATEUR
FOR EACH ROW
EXECUTE FUNCTION verif_email_format();

CREATE OR REPLACE FUNCTION verif_modification_status()
RETURNS TRIGGER AS $$
BEGIN
    -- Vérifier qu'on ne peut pas bannir un utilisateur déjà banni
    IF OLD.status = 'bannis' AND NEW.status = 'bannis' THEN
        RAISE EXCEPTION USING
            ERRCODE = 'SA018',
            MESSAGE = 'Impossible de bannir un utilisateur déjà banni';
    END IF;

    -- Vérifier qu'on ne peut pas suspendre un utilisateur déjà suspendu
    IF OLD.status = 'suspendu' AND NEW.status = 'suspendu' THEN
        RAISE EXCEPTION USING
            ERRCODE = 'SA019',
            MESSAGE = 'Impossible de suspendre un utilisateur déjà suspendu';
    END IF;

    -- Vérifier qu'on ne peut pas bannir ou suspendre un admin
    IF OLD.type_compte = 'admin' AND NEW.status IN ('bannis', 'suspendu') THEN
        RAISE EXCEPTION USING
            ERRCODE = 'SA020',
            MESSAGE = 'Impossible de bannir ou suspendre un administrateur';
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_verif_modification_status
BEFORE UPDATE OF status ON UTILISATEUR
FOR EACH ROW
EXECUTE FUNCTION verif_modification_status();

-- Fonction helper pour recalculer les statistiques d'avis d'un vendeur
CREATE OR REPLACE FUNCTION update_user_review_stats(p_id_vendeur UUID)
RETURNS VOID AS $$
DECLARE
    v_count INTEGER;
    v_avg DECIMAL(2,1);
BEGIN
    -- Compter et calculer la moyenne des avis
    SELECT COUNT(*), COALESCE(ROUND(AVG(note), 1), 0.0)
    INTO v_count, v_avg
    FROM REVIEW
    WHERE id_utilisateur_vendeur = p_id_vendeur;

    -- Mettre à jour les champs dans UTILISATEUR
    UPDATE UTILISATEUR
    SET nombre_avis = v_count,
        note_moyenne = v_avg
    WHERE id_utilisateur = p_id_vendeur;
END;
$$ LANGUAGE plpgsql;

-- Trigger après insertion d'un avis
CREATE OR REPLACE FUNCTION trg_review_insert_update_stats()
RETURNS TRIGGER AS $$
BEGIN
    PERFORM update_user_review_stats(NEW.id_utilisateur_vendeur);
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_review_after_insert
AFTER INSERT ON REVIEW
FOR EACH ROW
EXECUTE FUNCTION trg_review_insert_update_stats();

-- Trigger après modification d'un avis
CREATE OR REPLACE FUNCTION trg_review_update_update_stats()
RETURNS TRIGGER AS $$
BEGIN
    -- Si le vendeur change (cas rare mais possible), mettre à jour les deux
    IF OLD.id_utilisateur_vendeur != NEW.id_utilisateur_vendeur THEN
        PERFORM update_user_review_stats(OLD.id_utilisateur_vendeur);
        PERFORM update_user_review_stats(NEW.id_utilisateur_vendeur);
    ELSE
        PERFORM update_user_review_stats(NEW.id_utilisateur_vendeur);
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_review_after_update
AFTER UPDATE ON REVIEW
FOR EACH ROW
EXECUTE FUNCTION trg_review_update_update_stats();

-- Trigger après suppression d'un avis
CREATE OR REPLACE FUNCTION trg_review_delete_update_stats()
RETURNS TRIGGER AS $$
BEGIN
    PERFORM update_user_review_stats(OLD.id_utilisateur_vendeur);
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_review_after_delete
AFTER DELETE ON REVIEW
FOR EACH ROW
EXECUTE FUNCTION trg_review_delete_update_stats();

-- =============================================================================
-- TRIGGERS: Mise à jour automatique des statistiques de vente (montant_total_ventes)
-- =============================================================================

-- Fonction helper pour recalculer les montants de ventes d'un vendeur (total, mois, année)
CREATE OR REPLACE FUNCTION update_user_sales_stats(p_id_vendeur UUID)
RETURNS VOID AS $$
DECLARE
    v_total DECIMAL(12,2);
    v_mois DECIMAL(12,2);
    v_annee DECIMAL(12,2);
BEGIN
    -- Calculer le montant total des ventes validées
    SELECT
        COALESCE(SUM(p.montant_paye), 0.00) as total,
        COALESCE(SUM(CASE
            WHEN EXTRACT(MONTH FROM c.date) = EXTRACT(MONTH FROM CURRENT_DATE)
             AND EXTRACT(YEAR FROM c.date) = EXTRACT(YEAR FROM CURRENT_DATE)
            THEN p.montant_paye
            ELSE 0
        END), 0.00) as mois,
        COALESCE(SUM(CASE
            WHEN EXTRACT(YEAR FROM c.date) = EXTRACT(YEAR FROM CURRENT_DATE)
            THEN p.montant_paye
            ELSE 0
        END), 0.00) as annee
    INTO v_total, v_mois, v_annee
    FROM ANNONCE a
    JOIN DETAILLER_COMMANDE dc ON a.id_annonce = dc.id_annonce
    JOIN COMMANDE c ON dc.id_commande = c.id_commande
    JOIN PAIEMENT p ON c.id_paiement = p.id_paiement
    WHERE a.id_utilisateur_vendeur = p_id_vendeur
      AND p.statut = 'valide';

    -- Mettre à jour les champs dans UTILISATEUR
    UPDATE UTILISATEUR
    SET montant_total_ventes = v_total,
        montant_mois_actuel = v_mois,
        montant_annee_actuelle = v_annee
    WHERE id_utilisateur = p_id_vendeur;
END;
$$ LANGUAGE plpgsql;

-- Trigger après modification du statut d'un paiement (SEUL moment où les stats doivent changer!)
CREATE OR REPLACE FUNCTION trg_sales_after_paiement_update()
RETURNS TRIGGER AS $$
DECLARE
    v_id_vendeur UUID;
BEGIN
    -- Si le statut du paiement a changé (valide <-> non-valide)
    IF OLD.statut != NEW.statut THEN
        -- Parcourir tous les vendeurs concernés par ce paiement
        FOR v_id_vendeur IN
            SELECT DISTINCT a.id_utilisateur_vendeur
            FROM COMMANDE c
            JOIN DETAILLER_COMMANDE dc ON c.id_commande = dc.id_commande
            JOIN ANNONCE a ON dc.id_annonce = a.id_annonce
            WHERE c.id_paiement = NEW.id_paiement
        LOOP
            PERFORM update_user_sales_stats(v_id_vendeur);
        END LOOP;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_sales_after_paiement_update
AFTER UPDATE OF statut ON PAIEMENT
FOR EACH ROW
EXECUTE FUNCTION trg_sales_after_paiement_update();

-- Trigger après insertion d'une ligne de commande (quand produit vendu est lié à commande)
CREATE OR REPLACE FUNCTION trg_sales_after_detailler_commande()
RETURNS TRIGGER AS $$
DECLARE
    v_id_vendeur UUID;
    v_paiement_statut VARCHAR(20);
BEGIN
    -- Récupérer le vendeur de l'annonce et le statut du paiement
    SELECT a.id_utilisateur_vendeur, p.statut
    INTO v_id_vendeur, v_paiement_statut
    FROM ANNONCE a
    JOIN COMMANDE c ON c.id_commande = NEW.id_commande
    JOIN PAIEMENT p ON c.id_paiement = p.id_paiement
    WHERE a.id_annonce = NEW.id_annonce;

    -- Si le paiement est validé, mettre à jour les stats du vendeur
    IF v_paiement_statut = 'valide' THEN
        PERFORM update_user_sales_stats(v_id_vendeur);
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_sales_after_detailler_commande_insert
AFTER INSERT ON DETAILLER_COMMANDE
FOR EACH ROW
EXECUTE FUNCTION trg_sales_after_detailler_commande();
