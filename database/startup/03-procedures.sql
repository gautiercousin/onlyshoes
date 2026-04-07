
-- 1. UTILISATEUR - Gestion des utilisateurs
-- Créer un compte utilisateur (Exigence 6)
CREATE OR REPLACE FUNCTION utilisateur_create(p_data jsonb)
RETURNS utilisateur AS $$
DECLARE
    v_rec utilisateur%ROWTYPE;
BEGIN
    -- Insérer l'utilisateur
    INSERT INTO UTILISATEUR (nom, prenom, email, mdp, type_compte, status)
    VALUES (
        p_data->>'nom',
        p_data->>'prenom',
        p_data->>'email',
        p_data->>'mdp',  -- Doit être haché côté PHP (bcrypt)
        COALESCE(p_data->>'type_compte', 'standard'),
        COALESCE(p_data->>'status', 'actif')
    )
    RETURNING * INTO v_rec;

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Lire un utilisateur par ID
CREATE OR REPLACE FUNCTION utilisateur_read(p_id uuid)
RETURNS utilisateur AS $$
BEGIN
    RETURN (SELECT u FROM UTILISATEUR u WHERE id_utilisateur = p_id);
END;
$$ LANGUAGE plpgsql;

-- Modifier les informations d'un utilisateur (Exigence 9)
CREATE OR REPLACE FUNCTION utilisateur_update(p_id uuid, p_data jsonb)
RETURNS utilisateur AS $$
DECLARE
    v_rec utilisateur%ROWTYPE;
BEGIN
    UPDATE UTILISATEUR SET
        nom = COALESCE(p_data->>'nom', nom),
        prenom = COALESCE(p_data->>'prenom', prenom),
        email = COALESCE(p_data->>'email', email),
        mdp = COALESCE(p_data->>'mdp', mdp),
        type_compte = COALESCE(p_data->>'type_compte', type_compte),
        status = COALESCE(p_data->>'status', status)
    WHERE id_utilisateur = p_id
    RETURNING * INTO v_rec;

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Supprimer un compte utilisateur (Exigence 10 - RGPD)
-- Si l'utilisateur a des commandes livrées, il sera anonymisé au lieu d'être supprimé
CREATE OR REPLACE FUNCTION utilisateur_delete(p_id uuid)
RETURNS boolean AS $$
DECLARE
    v_has_delivered_orders BOOLEAN;
BEGIN
    -- Vérifier si l'utilisateur a des commandes livrées
    SELECT EXISTS(
        SELECT 1 FROM COMMANDE 
        WHERE id_utilisateur = p_id 
        AND statut = 'livree'
    ) INTO v_has_delivered_orders;
    
    IF v_has_delivered_orders THEN
        -- Anonymiser l'utilisateur au lieu de le supprimer
        UPDATE UTILISATEUR 
        SET 
            nom = 'Supprimé',
            prenom = 'Utilisateur',
            email = 'deleted_' || p_id || '@anonymized.local',
            mdp = 'ACCOUNT_DELETED',
            status = 'bannis'
        WHERE id_utilisateur = p_id;
        RETURN FOUND;
    ELSE
        -- Supprimer normalement si pas de commandes livrées
        DELETE FROM UTILISATEUR WHERE id_utilisateur = p_id;
        RETURN FOUND;
    END IF;
END;
$$ LANGUAGE plpgsql;

-- Lister/rechercher des utilisateurs avec filtres
CREATE OR REPLACE FUNCTION utilisateur_list(p_filters jsonb DEFAULT NULL)
RETURNS SETOF utilisateur AS $$
BEGIN
    RETURN QUERY
    SELECT * FROM UTILISATEUR
    WHERE (p_filters->>'type_compte' IS NULL OR type_compte = p_filters->>'type_compte')
      AND (p_filters->>'status' IS NULL OR status = p_filters->>'status')
      AND (p_filters->>'search' IS NULL OR (
          nom ILIKE '%' || (p_filters->>'search') || '%' OR
          prenom ILIKE '%' || (p_filters->>'search') || '%' OR
          email ILIKE '%' || (p_filters->>'search') || '%'
      ))
    ORDER BY date_creation DESC;
END;
$$ LANGUAGE plpgsql;

-- Authentification utilisateur (Exigence 7)
CREATE OR REPLACE FUNCTION utilisateur_login(p_email text, p_password text)
RETURNS utilisateur AS $$
DECLARE
    v_user utilisateur%ROWTYPE;
BEGIN
    -- Note: La vérification du mot de passe bcrypt doit être faite en PHP
    -- Cette fonction retourne juste l'utilisateur si l'email existe
    SELECT u INTO v_user FROM UTILISATEUR u WHERE email = p_email;
    
    -- Vérifier que l'utilisateur n'est pas banni ou suspendu
    IF v_user.status = 'bannis' THEN
        RAISE EXCEPTION USING
            ERRCODE = 'SA021',
            MESSAGE = 'Ce compte a été banni et ne peut plus se connecter';
    END IF;
    
    IF v_user.status = 'suspendu' THEN
        RAISE EXCEPTION USING
            ERRCODE = 'SA022',
            MESSAGE = 'Ce compte est temporairement suspendu';
    END IF;
    
    RETURN v_user;
END;
$$ LANGUAGE plpgsql;

-- Historique des achats (Exigence 16)
CREATE OR REPLACE FUNCTION utilisateur_get_historique_achats(p_id uuid)
RETURNS TABLE (
    id_commande uuid,
    date_commande timestamp,
    statut varchar,
    montant_total decimal,
    nb_articles bigint
) AS $$
BEGIN
    RETURN QUERY
    SELECT
        c.id_commande,
        c.date as date_commande,
        c.statut,
        p.montant_paye as montant_total,
        COUNT(dc.id_annonce) as nb_articles
    FROM COMMANDE c
    JOIN PAIEMENT p ON c.id_paiement = p.id_paiement
    LEFT JOIN DETAILLER_COMMANDE dc ON c.id_commande = dc.id_commande
    WHERE c.id_utilisateur = p_id
    GROUP BY c.id_commande, c.date, c.statut, p.montant_paye
    ORDER BY c.date DESC;
END;
$$ LANGUAGE plpgsql;

-- 2. ADRESSE - Gestion des adresses
-- Créer une adresse
CREATE OR REPLACE FUNCTION adresse_create(p_id_utilisateur uuid, p_data jsonb)
RETURNS adresse AS $$
DECLARE
    v_rec adresse%ROWTYPE;
BEGIN
    INSERT INTO ADRESSE (rue1, rue2, code_postal, ville, pays, id_utilisateur)
    VALUES (
        p_data->>'rue1',
        p_data->>'rue2',
        p_data->>'code_postal',
        p_data->>'ville',
        COALESCE(p_data->>'pays', 'France'),
        p_id_utilisateur
    )
    RETURNING * INTO v_rec;

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Lire une adresse
CREATE OR REPLACE FUNCTION adresse_read(p_id int)
RETURNS adresse AS $$
BEGIN
    RETURN (SELECT a FROM ADRESSE a WHERE id_adresse = p_id);
END;
$$ LANGUAGE plpgsql;

-- Modifier une adresse
CREATE OR REPLACE FUNCTION adresse_update(p_id int, p_data jsonb)
RETURNS adresse AS $$
DECLARE
    v_rec adresse%ROWTYPE;
BEGIN
    UPDATE ADRESSE SET
        rue1 = COALESCE(p_data->>'rue1', rue1),
        rue2 = COALESCE(p_data->>'rue2', rue2),
        code_postal = COALESCE(p_data->>'code_postal', code_postal),
        ville = COALESCE(p_data->>'ville', ville),
        pays = COALESCE(p_data->>'pays', pays)
    WHERE id_adresse = p_id
    RETURNING * INTO v_rec;

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Supprimer une adresse
CREATE OR REPLACE FUNCTION adresse_delete(p_id int)
RETURNS boolean AS $$
BEGIN
    DELETE FROM ADRESSE WHERE id_adresse = p_id;
    RETURN FOUND;
END;
$$ LANGUAGE plpgsql;

-- Lister les adresses d'un utilisateur
CREATE OR REPLACE FUNCTION adresse_list_by_user(p_id_utilisateur uuid)
RETURNS SETOF adresse AS $$
BEGIN
    RETURN QUERY
    SELECT * FROM ADRESSE
    WHERE id_utilisateur = p_id_utilisateur
    ORDER BY id_adresse DESC;
END;
$$ LANGUAGE plpgsql;

-- 3. ANNONCE - Gestion des annonces
-- Publier une annonce (Exigences 20-23)
-- Note: Vous devez d'abord créer une image, puis passer son id_image dans p_data.
CREATE OR REPLACE FUNCTION annonce_create(p_id_vendeur uuid, p_data jsonb)
RETURNS annonce AS $$
DECLARE
    v_rec annonce%ROWTYPE;
    v_image_id INTEGER;
BEGIN
    -- If no image ID provided, create a default placeholder image
    IF p_data->>'id_image' IS NULL THEN
        INSERT INTO IMAGE (url, est_principale)
        VALUES ('/images/placeholder.jpg', TRUE)
        RETURNING id_image INTO v_image_id;
    ELSE
        v_image_id := (p_data->>'id_image')::integer;
    END IF;

    INSERT INTO ANNONCE (
        titre, description, prix, etat, taille_systeme, taille,
        disponible, id_couleur, id_materiau, id_marque, id_image, id_utilisateur_vendeur
    )
    VALUES (
        p_data->>'titre',
        p_data->>'description',
        (p_data->>'prix')::decimal,
        p_data->>'etat',
        p_data->>'taille_systeme',
        p_data->>'taille',
        COALESCE((p_data->>'disponible')::boolean, TRUE),
        (p_data->>'id_couleur')::integer,
        (p_data->>'id_materiau')::integer,
        (p_data->>'id_marque')::integer,
        v_image_id,
        p_id_vendeur
    )
    RETURNING * INTO v_rec;

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Consulter une annonce (Exigence 2)
CREATE OR REPLACE FUNCTION annonce_read(p_id uuid)
RETURNS annonce AS $$
BEGIN
    RETURN (SELECT a FROM ANNONCE a WHERE id_annonce = p_id);
END;
$$ LANGUAGE plpgsql;

-- Consulter une annonce avec détails (version enrichie)
CREATE OR REPLACE FUNCTION annonce_read_details(p_id uuid)
RETURNS TABLE (
    id_annonce uuid,
    titre varchar,
    description text,
    prix decimal,
    etat varchar,
    taille_systeme varchar,
    taille varchar,
    date_publication timestamp,
    disponible boolean,
    couleur varchar,
    materiau varchar,
    marque varchar,
    vendeur_nom varchar,
    vendeur_prenom varchar,
    image_principale varchar
) AS $$
BEGIN
    RETURN QUERY
    SELECT
        a.id_annonce,
        a.titre,
        a.description,
        a.prix,
        a.etat,
        a.taille_systeme,
        a.taille,
        a.date_publication,
        a.disponible,
        c.nom as couleur,
        m.nom as materiau,
        ma.nom as marque,
        u.nom as vendeur_nom,
        u.prenom as vendeur_prenom,
        i.url as image_principale
    FROM ANNONCE a
    JOIN COULEUR c ON a.id_couleur = c.id_couleur
    JOIN MATERIAU m ON a.id_materiau = m.id_materiau
    JOIN MARQUE ma ON a.id_marque = ma.id_marque
    JOIN UTILISATEUR u ON a.id_utilisateur_vendeur = u.id_utilisateur
    JOIN IMAGE i ON a.id_image = i.id_image
    WHERE a.id_annonce = p_id;
END;
$$ LANGUAGE plpgsql;

-- Modifier une annonce (Exigence 21)
CREATE OR REPLACE FUNCTION annonce_update(p_id uuid, p_data jsonb)
RETURNS annonce AS $$
DECLARE
    v_rec annonce%ROWTYPE;
BEGIN
    UPDATE ANNONCE SET
        titre = COALESCE(p_data->>'titre', titre),
        description = COALESCE(p_data->>'description', description),
        prix = COALESCE((p_data->>'prix')::decimal, prix),
        etat = COALESCE(p_data->>'etat', etat),
        taille_systeme = COALESCE(p_data->>'taille_systeme', taille_systeme),
        taille = COALESCE(p_data->>'taille', taille),
        disponible = COALESCE((p_data->>'disponible')::boolean, disponible),
        id_couleur = COALESCE((p_data->>'id_couleur')::integer, id_couleur),
        id_materiau = COALESCE((p_data->>'id_materiau')::integer, id_materiau),
        id_marque = COALESCE((p_data->>'id_marque')::integer, id_marque)
    WHERE id_annonce = p_id
    RETURNING * INTO v_rec;

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Supprimer une annonce (Exigence 21)
CREATE OR REPLACE FUNCTION annonce_delete(p_id uuid)
RETURNS boolean AS $$
BEGIN
    DELETE FROM ANNONCE WHERE id_annonce = p_id;
    RETURN FOUND;
END;
$$ LANGUAGE plpgsql;

-- Rechercher/filtrer/trier les annonces (Exigences 3-4)
-- Exclut les annonces des utilisateurs bannis ou suspendus
CREATE OR REPLACE FUNCTION annonce_list(p_filters jsonb DEFAULT NULL)
RETURNS SETOF annonce AS $$
BEGIN
    RETURN QUERY
    SELECT a.* FROM ANNONCE a
    INNER JOIN UTILISATEUR u ON a.id_utilisateur_vendeur = u.id_utilisateur
    WHERE a.disponible = TRUE
      AND u.status = 'actif'
      AND (p_filters->>'search' IS NULL OR (
          a.titre ILIKE '%' || (p_filters->>'search') || '%' OR
          a.description ILIKE '%' || (p_filters->>'search') || '%'
      ))
      AND (p_filters->>'id_marque' IS NULL OR a.id_marque = (p_filters->>'id_marque')::int)
      AND (p_filters->>'id_couleur' IS NULL OR a.id_couleur = (p_filters->>'id_couleur')::int)
      AND (p_filters->>'id_materiau' IS NULL OR a.id_materiau = (p_filters->>'id_materiau')::int)
      AND (p_filters->>'prix_min' IS NULL OR a.prix >= (p_filters->>'prix_min')::decimal)
      AND (p_filters->>'prix_max' IS NULL OR a.prix <= (p_filters->>'prix_max')::decimal)
      AND (p_filters->>'etat' IS NULL OR a.etat = p_filters->>'etat')
      AND (p_filters->>'taille_systeme' IS NULL OR a.taille_systeme = p_filters->>'taille_systeme')
      AND (p_filters->>'taille' IS NULL OR a.taille = p_filters->>'taille')
    ORDER BY
      CASE WHEN COALESCE(p_filters->>'sort', 'date_publication') = 'prix_asc' THEN a.prix END ASC,
      CASE WHEN COALESCE(p_filters->>'sort', 'date_publication') = 'prix_desc' THEN a.prix END DESC,
      a.date_publication DESC;
END;
$$ LANGUAGE plpgsql;

-- Marquer une annonce comme indisponible
CREATE OR REPLACE FUNCTION annonce_marquer_indisponible(p_id uuid)
RETURNS boolean AS $$
BEGIN
    UPDATE ANNONCE SET disponible = FALSE WHERE id_annonce = p_id;
    RETURN FOUND;
END;
$$ LANGUAGE plpgsql;

-- Lister les annonces d'un vendeur
-- Retourne toutes les annonces si appelé pour le vendeur lui-même
-- Retourne uniquement les annonces disponibles d'un vendeur actif si appelé par d'autres
CREATE OR REPLACE FUNCTION annonce_list_by_vendeur(p_id_vendeur uuid)
RETURNS SETOF annonce AS $$
BEGIN
    RETURN QUERY
    SELECT a.* FROM ANNONCE a
    INNER JOIN UTILISATEUR u ON a.id_utilisateur_vendeur = u.id_utilisateur
    WHERE a.id_utilisateur_vendeur = p_id_vendeur
    ORDER BY a.date_publication DESC;
END;
$$ LANGUAGE plpgsql;

-- 4. IMAGE - Gestion des images
-- Ajouter une image (renvoie l'id à utiliser lors de la création de l'annonce)
CREATE OR REPLACE FUNCTION image_create(p_id_annonce uuid, p_data jsonb)
RETURNS image AS $$
DECLARE
    v_rec image%ROWTYPE;
BEGIN
    INSERT INTO IMAGE (url, est_principale)
    VALUES (
        p_data->>'url',
        COALESCE((p_data->>'est_principale')::boolean, FALSE)
    )
    RETURNING * INTO v_rec;

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Supprimer une image
CREATE OR REPLACE FUNCTION image_delete(p_id int)
RETURNS boolean AS $$
BEGIN
    DELETE FROM IMAGE WHERE id_image = p_id;
    RETURN FOUND;
END;
$$ LANGUAGE plpgsql;

-- Définir une image comme principale
CREATE OR REPLACE FUNCTION image_set_principale(p_id int, p_id_annonce uuid)
RETURNS boolean AS $$
BEGIN
    -- Retirer le statut principal de toutes les images de cette annonce
    UPDATE IMAGE SET est_principale = FALSE
    WHERE id_image IN (
        SELECT id_image FROM ANNONCE WHERE id_annonce = p_id_annonce
    );

    -- Définir la nouvelle image principale
    UPDATE IMAGE SET est_principale = TRUE WHERE id_image = p_id;

    RETURN FOUND;
END;
$$ LANGUAGE plpgsql;

-- Lister les images d'une annonce (renvoie l'image unique)
CREATE OR REPLACE FUNCTION image_list_by_annonce(p_id_annonce uuid)
RETURNS SETOF image AS $$
BEGIN
    RETURN QUERY
    SELECT i.* FROM IMAGE i
    JOIN ANNONCE a ON i.id_image = a.id_image
    WHERE a.id_annonce = p_id_annonce;
END;
$$ LANGUAGE plpgsql;

-- 6. COMMANDE - Gestion des commandes
-- Créer une commande directement avec les articles (Exigence 13)
-- Format p_articles: jsonb array like [{"id_annonce": "uuid", "quantite": 1}, ...]
CREATE OR REPLACE FUNCTION commande_create(p_id_utilisateur uuid, p_articles jsonb, p_montant decimal)
RETURNS commande AS $$
DECLARE
    v_rec commande%ROWTYPE;
    v_paiement_id int;
    v_article jsonb;
    v_ligne_commande_id int;
    v_prix decimal;
BEGIN
    -- Créer le paiement
    INSERT INTO PAIEMENT (type, statut, montant_paye)
    VALUES ('carte_bancaire', 'en_attente', p_montant)
    RETURNING id_paiement INTO v_paiement_id;

    -- Créer la commande
    INSERT INTO COMMANDE (id_utilisateur, id_paiement, statut)
    VALUES (p_id_utilisateur, v_paiement_id, 'en_preparation')
    RETURNING * INTO v_rec;

    -- Ajouter les articles à la commande
    FOR v_article IN SELECT * FROM jsonb_array_elements(p_articles)
    LOOP
        -- Récupérer le prix de l'annonce
        SELECT prix INTO v_prix FROM ANNONCE WHERE id_annonce = (v_article->>'id_annonce')::uuid;

        -- Créer la ligne de commande
        INSERT INTO LIGNE_COMMANDE (prix, quantite)
        VALUES (v_prix, (v_article->>'quantite')::int)
        RETURNING id_ligne_commande INTO v_ligne_commande_id;

        -- Lier à la commande
        INSERT INTO DETAILLER_COMMANDE (id_commande, id_annonce, id_ligne_commande)
        VALUES (v_rec.id_commande, (v_article->>'id_annonce')::uuid, v_ligne_commande_id);
    END LOOP;

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Consulter une commande
CREATE OR REPLACE FUNCTION commande_read(p_id uuid)
RETURNS commande AS $$
BEGIN
    RETURN (SELECT c FROM COMMANDE c WHERE id_commande = p_id);
END;
$$ LANGUAGE plpgsql;

-- Version enrichie avec détails
CREATE OR REPLACE FUNCTION commande_read_details(p_id uuid)
RETURNS TABLE (
    id_commande uuid,
    date_commande timestamp,
    statut varchar,
    montant_total decimal,
    type_paiement varchar,
    statut_paiement varchar,
    nb_articles bigint
) AS $$
BEGIN
    RETURN QUERY
    SELECT
        c.id_commande,
        c.date as date_commande,
        c.statut,
        p.montant_paye as montant_total,
        p.type as type_paiement,
        p.statut as statut_paiement,
        COUNT(dc.id_annonce) as nb_articles
    FROM COMMANDE c
    JOIN PAIEMENT p ON c.id_paiement = p.id_paiement
    LEFT JOIN DETAILLER_COMMANDE dc ON c.id_commande = dc.id_commande
    WHERE c.id_commande = p_id
    GROUP BY c.id_commande, c.date, c.statut, p.montant_paye, p.type, p.statut;
END;
$$ LANGUAGE plpgsql;

-- Mettre à jour le statut d'une commande
CREATE OR REPLACE FUNCTION commande_update_statut(p_id uuid, p_statut text)
RETURNS commande AS $$
DECLARE
    v_rec commande%ROWTYPE;
BEGIN
    UPDATE COMMANDE SET statut = p_statut
    WHERE id_commande = p_id
    RETURNING * INTO v_rec;

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Annuler une commande
CREATE OR REPLACE FUNCTION commande_annuler(p_id uuid)
RETURNS boolean AS $$
BEGIN
    UPDATE COMMANDE SET statut = 'annulee' WHERE id_commande = p_id;
    UPDATE PAIEMENT SET statut = 'rembourse'
    WHERE id_paiement = (SELECT id_paiement FROM COMMANDE WHERE id_commande = p_id);
    RETURN FOUND;
END;
$$ LANGUAGE plpgsql;

-- Lister les commandes d'un utilisateur (Exigence 16)
CREATE OR REPLACE FUNCTION commande_list_by_user(p_id_utilisateur uuid)
RETURNS SETOF commande AS $$
BEGIN
    RETURN QUERY
    SELECT * FROM COMMANDE
    WHERE id_utilisateur = p_id_utilisateur
    ORDER BY date DESC;
END;
$$ LANGUAGE plpgsql;

-- 8. PAIEMENT - Gestion des paiements
-- Créer un paiement (Exigences 14, 25)
CREATE OR REPLACE FUNCTION paiement_create(p_data jsonb)
RETURNS paiement AS $$
DECLARE
    v_rec paiement%ROWTYPE;
BEGIN
    INSERT INTO PAIEMENT (type, statut, montant_paye)
    VALUES (
        p_data->>'type',
        COALESCE(p_data->>'statut', 'en_attente'),
        (p_data->>'montant_paye')::decimal
    )
    RETURNING * INTO v_rec;

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Valider un paiement
CREATE OR REPLACE FUNCTION paiement_valider(p_id int)
RETURNS paiement AS $$
DECLARE
    v_rec paiement%ROWTYPE;
BEGIN
    UPDATE PAIEMENT SET statut = 'valide', date = CURRENT_TIMESTAMP
    WHERE id_paiement = p_id
    RETURNING * INTO v_rec;

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Annuler/rembourser un paiement
CREATE OR REPLACE FUNCTION paiement_annuler(p_id int)
RETURNS paiement AS $$
DECLARE
    v_rec paiement%ROWTYPE;
BEGIN
    UPDATE PAIEMENT SET statut = 'rembourse'
    WHERE id_paiement = p_id
    RETURNING * INTO v_rec;

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Lire un paiement
CREATE OR REPLACE FUNCTION paiement_read(p_id integer)
RETURNS paiement AS $$
BEGIN
    RETURN (SELECT p FROM PAIEMENT p WHERE id_paiement = p_id);
END;
$$ LANGUAGE plpgsql;

-- Envoyer confirmation de paiement (Exigences 15, 26)
-- Note: L'envoi d'email sera géré côté PHP
CREATE OR REPLACE FUNCTION paiement_get_info_confirmation(p_id int)
RETURNS TABLE (
    email_client text,
    email_vendeur text,
    montant decimal,
    date_paiement timestamp
) AS $$
BEGIN
    RETURN QUERY
    SELECT
        u_client.email as email_client,
        u_vendeur.email as email_vendeur,
        p.montant_paye as montant,
        p.date as date_paiement
    FROM PAIEMENT p
    JOIN COMMANDE c ON p.id_paiement = c.id_paiement
    JOIN UTILISATEUR u_client ON c.id_utilisateur = u_client.id_utilisateur
    JOIN DETAILLER_COMMANDE dc ON c.id_commande = dc.id_commande
    JOIN ANNONCE a ON dc.id_annonce = a.id_annonce
    JOIN UTILISATEUR u_vendeur ON a.id_utilisateur_vendeur = u_vendeur.id_utilisateur
    WHERE p.id_paiement = p_id
    LIMIT 1;
END;
$$ LANGUAGE plpgsql;

-- 9. REVIEW - Gestion des avis
-- Écrire une review (Exigence 19)
-- Règles métier :
--   1. L'acheteur doit avoir acheté au moins un produit du vendeur
--   2. Un seul avis autorisé par couple acheteur-vendeur
CREATE OR REPLACE FUNCTION review_create(p_id_auteur uuid, p_id_vendeur uuid, p_data jsonb)
RETURNS review AS $$
DECLARE
    v_rec review%ROWTYPE;
    v_existing_review_count INT;
    v_purchase_count INT;
BEGIN
    -- Vérifier qu'il n'existe pas déjà un avis de cet acheteur pour ce vendeur
    SELECT COUNT(*) INTO v_existing_review_count
    FROM REVIEW
    WHERE id_utilisateur_auteur = p_id_auteur
      AND id_utilisateur_vendeur = p_id_vendeur;

    IF v_existing_review_count > 0 THEN
        RAISE EXCEPTION USING
            ERRCODE = 'SA023',
            MESSAGE = 'Vous avez déjà laissé un avis pour ce vendeur';
    END IF;

    -- Vérifier que l'acheteur a bien acheté au moins un produit de ce vendeur
    SELECT COUNT(DISTINCT c.id_commande) INTO v_purchase_count
    FROM COMMANDE c
    JOIN DETAILLER_COMMANDE dc ON c.id_commande = dc.id_commande
    JOIN ANNONCE a ON dc.id_annonce = a.id_annonce
    WHERE c.id_utilisateur = p_id_auteur
      AND a.id_utilisateur_vendeur = p_id_vendeur
      AND c.statut != 'annulee';

    IF v_purchase_count = 0 THEN
        RAISE EXCEPTION USING
            ERRCODE = 'SA024',
            MESSAGE = 'Vous devez avoir acheté au moins un produit de ce vendeur pour laisser un avis';
    END IF;

    -- Créer l'avis
    INSERT INTO REVIEW (note, commentaire, id_utilisateur_auteur, id_utilisateur_vendeur)
    VALUES (
        (p_data->>'note')::int,
        p_data->>'commentaire',
        p_id_auteur,
        p_id_vendeur
    )
    RETURNING * INTO v_rec;

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Lire une review
CREATE OR REPLACE FUNCTION review_read(p_id uuid)
RETURNS review AS $$
BEGIN
    RETURN (SELECT r FROM REVIEW r WHERE id_review = p_id);
END;
$$ LANGUAGE plpgsql;

-- Modifier une review
CREATE OR REPLACE FUNCTION review_update(p_id uuid, p_data jsonb)
RETURNS review AS $$
DECLARE
    v_rec review%ROWTYPE;
BEGIN
    UPDATE REVIEW SET
        note = COALESCE((p_data->>'note')::int, note),
        commentaire = COALESCE(p_data->>'commentaire', commentaire)
    WHERE id_review = p_id
    RETURNING * INTO v_rec;

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Supprimer une review
CREATE OR REPLACE FUNCTION review_delete(p_id uuid)
RETURNS boolean AS $$
BEGIN
    DELETE FROM REVIEW WHERE id_review = p_id;
    RETURN FOUND;
END;
$$ LANGUAGE plpgsql;

-- Lister les reviews d'un vendeur (Exigence 18)
CREATE OR REPLACE FUNCTION review_list_by_vendeur(
    p_id_vendeur uuid,
    p_limit int DEFAULT NULL,
    p_offset int DEFAULT 0,
    p_exclude_review_id uuid DEFAULT NULL
)
RETURNS TABLE (
    id_review uuid,
    note int,
    commentaire text,
    date_review timestamp,
    auteur_nom varchar,
    auteur_prenom varchar,
    auteur_id uuid
) AS $$
BEGIN
    RETURN QUERY
    SELECT
        r.id_review,
        r.note,
        r.commentaire,
        r.date as date_review,
        u.nom as auteur_nom,
        u.prenom as auteur_prenom,
        u.id_utilisateur as auteur_id
    FROM REVIEW r
    JOIN UTILISATEUR u ON r.id_utilisateur_auteur = u.id_utilisateur
    WHERE r.id_utilisateur_vendeur = p_id_vendeur
      AND (p_exclude_review_id IS NULL OR r.id_review != p_exclude_review_id)
    ORDER BY r.date DESC
    LIMIT p_limit
    OFFSET p_offset;
END;
$$ LANGUAGE plpgsql;


-- Créer un signalement (Exigence 29a, version polymorphe)
-- p_type : 'user', 'annonce', 'review'
-- p_id_cible : UUID de la cible (user, annonce, review)
CREATE OR REPLACE FUNCTION signalement_create(p_id_auteur uuid, p_type text, p_id_cible uuid, p_data jsonb)
RETURNS signalement AS $$
DECLARE
    v_rec signalement%ROWTYPE;
BEGIN
    INSERT INTO SIGNALEMENT (
        motif, 
        description, 
        statut, 
        type, 
        id_review_cible, 
        id_utilisateur_cible, 
        id_annonce_cible, 
        id_utilisateur_auteur
    )
    VALUES (
        p_data->>'motif',
        p_data->>'description',
        'en_attente',
        p_type,
        CASE WHEN p_type = 'review' THEN p_id_cible ELSE NULL END,
        CASE WHEN p_type = 'user' THEN p_id_cible ELSE NULL END,
        CASE WHEN p_type = 'annonce' THEN p_id_cible ELSE NULL END,
        p_id_auteur
    )
    RETURNING * INTO v_rec;

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Traiter un signalement (Exigence 32)
CREATE OR REPLACE FUNCTION signalement_traiter(p_id int, p_decision text, p_raison text DEFAULT NULL)
RETURNS signalement AS $$
DECLARE
    v_rec signalement%ROWTYPE;
    v_id_admin uuid;
BEGIN
    -- Mettre à jour le statut
    UPDATE SIGNALEMENT SET statut = p_decision
    WHERE id_signalement = p_id
    RETURNING * INTO v_rec;

    -- Logger l'action admin (nécessite l'ID admin côté PHP)
    -- INSERT INTO ADMIN_LOG sera fait via admin_log_create() côté PHP

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;


-- Lister les signalements avec filtres (adapté pour type et nouvelles colonnes)
CREATE OR REPLACE FUNCTION signalement_list(p_filters jsonb DEFAULT NULL)
RETURNS SETOF signalement AS $$
BEGIN
        RETURN QUERY
        SELECT * FROM SIGNALEMENT
        WHERE (p_filters->>'statut' IS NULL OR statut = p_filters->>'statut')
            AND (p_filters->>'type' IS NULL OR type = p_filters->>'type')
            AND (p_filters->>'id_cible' IS NULL OR 
                 COALESCE(id_review_cible, id_utilisateur_cible, id_annonce_cible)::text = p_filters->>'id_cible')
        ORDER BY date DESC;
END;
$$ LANGUAGE plpgsql;

-- 11. CONSENTEMENT_UTILISATEUR - Gestion des consentements GDPR
-- Donner un consentement (Exigence 11)
CREATE OR REPLACE FUNCTION consentement_donner(p_id_utilisateur uuid, p_type text)
RETURNS consentement_utilisateur AS $$
DECLARE
    v_rec consentement_utilisateur%ROWTYPE;
BEGIN
    INSERT INTO CONSENTEMENT_UTILISATEUR (type_consentement, statut, id_utilisateur)
    VALUES (p_type, TRUE, p_id_utilisateur)
    RETURNING * INTO v_rec;

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Retirer un consentement
CREATE OR REPLACE FUNCTION consentement_retirer(p_id int)
RETURNS consentement_utilisateur AS $$
DECLARE
    v_rec consentement_utilisateur%ROWTYPE;
BEGIN
    UPDATE CONSENTEMENT_UTILISATEUR
    SET statut = FALSE, date_retrait = CURRENT_TIMESTAMP
    WHERE id_consentement = p_id
    RETURNING * INTO v_rec;

    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Lister les consentements d'un utilisateur
CREATE OR REPLACE FUNCTION consentement_list_by_user(p_id_utilisateur uuid)
RETURNS SETOF consentement_utilisateur AS $$
BEGIN
    RETURN QUERY
    SELECT * FROM CONSENTEMENT_UTILISATEUR
    WHERE id_utilisateur = p_id_utilisateur
    ORDER BY date_consentement DESC;
END;
$$ LANGUAGE plpgsql;

-- Créer un consentement
CREATE OR REPLACE FUNCTION consentement_create(p_id_utilisateur uuid, p_data jsonb)
RETURNS consentement_utilisateur AS $$
DECLARE
    v_rec consentement_utilisateur%ROWTYPE;
BEGIN
    INSERT INTO CONSENTEMENT_UTILISATEUR (type_consentement, statut, id_utilisateur)
    VALUES (
        p_data->>'type_consentement',
        COALESCE((p_data->>'statut')::boolean, TRUE),
        p_id_utilisateur
    )
    RETURNING * INTO v_rec;
    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- 12. ADMIN_LOG - Traçabilité des actions admin
-- Enregistrer une action admin (Exigences 29-32)
DROP FUNCTION IF EXISTS admin_log_create(jsonb);
CREATE OR REPLACE FUNCTION admin_log_create(p_id_admin uuid, p_data jsonb)
RETURNS admin_log AS $$
DECLARE
    v_rec admin_log%ROWTYPE;
BEGIN
    INSERT INTO ADMIN_LOG (action_type, id_cible, raison, ip_address, id_utilisateur)
    VALUES (
        p_data->>'action_type',
        p_data->>'id_cible',
        p_data->>'raison',
        p_data->>'ip_address',
        p_id_admin
    )
    RETURNING * INTO v_rec;
    RETURN v_rec;
END;
$$ LANGUAGE plpgsql;

-- Consulter les logs admin (Exigence 28)
CREATE OR REPLACE FUNCTION admin_log_list(p_filters jsonb DEFAULT NULL)
RETURNS SETOF admin_log AS $$
BEGIN
    RETURN QUERY
    SELECT * FROM ADMIN_LOG
    WHERE (p_filters->>'action_type' IS NULL OR action_type = p_filters->>'action_type')
      AND (p_filters->>'id_utilisateur' IS NULL OR id_utilisateur = (p_filters->>'id_utilisateur')::uuid)
    ORDER BY date_action DESC;
END;
$$ LANGUAGE plpgsql;

-- 13. TABLES DE RÉFÉRENCE - Consultation uniquement
-- Lister toutes les marques
CREATE OR REPLACE FUNCTION marque_list()
RETURNS SETOF marque AS $$
BEGIN
    RETURN QUERY SELECT * FROM MARQUE ORDER BY nom;
END;
$$ LANGUAGE plpgsql;

-- Lister toutes les couleurs
CREATE OR REPLACE FUNCTION couleur_list()
RETURNS SETOF couleur AS $$
BEGIN
    RETURN QUERY SELECT * FROM COULEUR ORDER BY nom;
END;
$$ LANGUAGE plpgsql;

-- Lister tous les matériaux
CREATE OR REPLACE FUNCTION materiau_list()
RETURNS SETOF materiau AS $$
BEGIN
    RETURN QUERY SELECT * FROM MATERIAU ORDER BY nom;
END;
$$ LANGUAGE plpgsql;

-- 14. STATISTIQUES - Tableau de bord vendeur
-- Statistiques de vente pour un vendeur (Exigence 24)
CREATE OR REPLACE FUNCTION vendeur_get_statistiques(p_id_vendeur uuid)
RETURNS TABLE (
    total_ventes bigint,
    montant_total decimal,
    note_moyenne numeric,
    nb_reviews bigint
) AS $$
BEGIN
    RETURN QUERY
    SELECT
        COUNT(DISTINCT c.id_commande) as total_ventes,
        COALESCE(SUM(p.montant_paye), 0) as montant_total,
        COALESCE(AVG(r.note), 0) as note_moyenne,
        COUNT(DISTINCT r.id_review) as nb_reviews
    FROM UTILISATEUR u
    LEFT JOIN ANNONCE a ON u.id_utilisateur = a.id_utilisateur_vendeur
    LEFT JOIN DETAILLER_COMMANDE dc ON a.id_annonce = dc.id_annonce
    LEFT JOIN COMMANDE c ON dc.id_commande = c.id_commande
    LEFT JOIN PAIEMENT p ON c.id_paiement = p.id_paiement AND p.statut = 'valide'
    LEFT JOIN REVIEW r ON u.id_utilisateur = r.id_utilisateur_vendeur
    WHERE u.id_utilisateur = p_id_vendeur
    GROUP BY u.id_utilisateur;
END;
$$ LANGUAGE plpgsql;

-- 15. AI EMBEDDINGS - Recherche sémantique avec pgvector
-- Mettre à jour les embeddings d'une annonce (appelé depuis PHP après génération Python)
CREATE OR REPLACE FUNCTION annonce_update_embeddings(p_id_annonce uuid, p_embeddings vector(384))
RETURNS boolean AS $$
BEGIN
    UPDATE ANNONCE SET embeddings = p_embeddings WHERE id_annonce = p_id_annonce;
    RETURN FOUND;
END;
$$ LANGUAGE plpgsql;

-- Recherche sémantique par vecteur avec filtres optionnels
CREATE OR REPLACE FUNCTION annonce_search_by_embedding(
    p_search_vector vector(384),
    p_limit integer DEFAULT 10,
    p_filters jsonb DEFAULT NULL
)
RETURNS TABLE (
    id_annonce uuid,
    titre varchar,
    description text,
    prix decimal,
    etat varchar,
    taille_systeme varchar,
    taille varchar,
    date_publication timestamp,
    id_couleur integer,
    id_materiau integer,
    id_marque integer,
    id_image integer,
    id_utilisateur_vendeur uuid,
    similarity_score float
) AS $$
BEGIN
    RETURN QUERY
    SELECT
        a.id_annonce,
        a.titre,
        a.description,
        a.prix,
        a.etat,
        a.taille_systeme,
        a.taille,
        a.date_publication,
        a.id_couleur,
        a.id_materiau,
        a.id_marque,
        a.id_image,
        a.id_utilisateur_vendeur,
        (1 - (a.embeddings <=> p_search_vector))::float as similarity_score
    FROM ANNONCE a
    WHERE a.embeddings IS NOT NULL
        AND a.disponible = TRUE
        AND (p_filters IS NULL OR p_filters->>'prix_min' IS NULL OR a.prix >= (p_filters->>'prix_min')::decimal)
        AND (p_filters IS NULL OR p_filters->>'prix_max' IS NULL OR a.prix <= (p_filters->>'prix_max')::decimal)
        AND (p_filters IS NULL OR p_filters->>'id_marque' IS NULL OR a.id_marque = (p_filters->>'id_marque')::integer)
        AND (p_filters IS NULL OR p_filters->>'id_couleur' IS NULL OR a.id_couleur = (p_filters->>'id_couleur')::integer)
        AND (p_filters IS NULL OR p_filters->>'id_materiau' IS NULL OR a.id_materiau = (p_filters->>'id_materiau')::integer)
        AND (p_filters IS NULL OR p_filters->>'etat' IS NULL OR a.etat = p_filters->>'etat')
        AND (p_filters IS NULL OR p_filters->>'taille_systeme' IS NULL OR a.taille_systeme = p_filters->>'taille_systeme')
        AND (p_filters IS NULL OR p_filters->>'taille' IS NULL OR a.taille = p_filters->>'taille')
    ORDER BY a.embeddings <=> p_search_vector
    LIMIT p_limit;
END;
$$ LANGUAGE plpgsql;

-- Trouver des produits similaires
CREATE OR REPLACE FUNCTION annonce_find_similar(
    p_id_annonce uuid,
    p_limit integer DEFAULT 5
)
RETURNS TABLE (
    id_annonce uuid,
    titre varchar,
    prix decimal,
    similarity_score float
) AS $$
BEGIN
    RETURN QUERY
    SELECT
        a.id_annonce,
        a.titre,
        a.prix,
        (1 - (a.embeddings <=> source.embeddings))::float as similarity_score
    FROM ANNONCE a
    CROSS JOIN (SELECT embeddings FROM ANNONCE src WHERE src.id_annonce = p_id_annonce) source
    WHERE a.id_annonce != p_id_annonce
        AND a.embeddings IS NOT NULL
        AND a.disponible = TRUE
    ORDER BY a.embeddings <=> source.embeddings
    LIMIT p_limit;
END;
$$ LANGUAGE plpgsql;
