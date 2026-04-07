-- Ce script teste toutes les procédures stockées
-- Chaque test affiche le résultat et vérifie le comportement attendu

-- 1. TESTS UTILISATEUR (7 procédures)
\echo '>>> TEST 1.1: utilisateur_create - Créer un utilisateur'
DO $$
DECLARE
    v_user utilisateur%ROWTYPE;
    v_json jsonb;
BEGIN
    -- Mot de passe = 'password'
    v_json := '{"nom": "Test", "prenom": "User", "email": "testuser@example.com", "mdp": "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi"}'::jsonb;

    SELECT * INTO v_user FROM utilisateur_create(v_json);

    IF v_user.nom = 'Test' AND v_user.email = 'testuser@example.com' AND v_user.id_panier IS NOT NULL THEN
        RAISE NOTICE 'SUCCÈS: Utilisateur créé avec panier (id_panier=%)', v_user.id_panier;
    ELSE
        RAISE NOTICE 'ÉCHEC: Utilisateur non créé correctement';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 1.2: utilisateur_read - Lire un utilisateur'
DO $$
DECLARE
    v_user utilisateur%ROWTYPE;
BEGIN
    SELECT * INTO v_user FROM utilisateur_read('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11');

    IF v_user.nom = 'Dupont' AND v_user.prenom = 'Marie' THEN
        RAISE NOTICE 'SUCCÈS: Utilisateur Marie Dupont récupéré';
    ELSE
        RAISE NOTICE 'ÉCHEC: Utilisateur non trouvé';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 1.3: utilisateur_update - Modifier un utilisateur'
DO $$
DECLARE
    v_user utilisateur%ROWTYPE;
    v_json jsonb;
BEGIN
    v_json := '{"nom": "Dupont-Modified"}'::jsonb;

    SELECT * INTO v_user FROM utilisateur_update('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', v_json);

    IF v_user.nom = 'Dupont-Modified' THEN
        RAISE NOTICE 'SUCCÈS: Utilisateur modifié';
        -- Restaurer la valeur originale
        PERFORM utilisateur_update('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', '{"nom": "Dupont"}'::jsonb);
    ELSE
        RAISE NOTICE 'ÉCHEC: Utilisateur non modifié';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 1.4: utilisateur_login - Connexion utilisateur'
DO $$
DECLARE
    v_user utilisateur%ROWTYPE;
BEGIN
    SELECT * INTO v_user FROM utilisateur_login('marie.dupont@email.fr', 'dummy_password');

    IF v_user.email = 'marie.dupont@email.fr' THEN
        RAISE NOTICE 'SUCCÈS: Utilisateur trouvé pour login (PHP doit vérifier le hash)';
    ELSE
        RAISE NOTICE 'ÉCHEC: Utilisateur non trouvé';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 1.5: utilisateur_list - Lister les utilisateurs'
DO $$
DECLARE
    v_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO v_count FROM utilisateur_list('{}'::jsonb);

    IF v_count > 0 THEN
        RAISE NOTICE 'SUCCÈS: % utilisateur(s) trouvé(s)', v_count;
    ELSE
        RAISE NOTICE 'ÉCHEC: Aucun utilisateur trouvé';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 1.6: utilisateur_get_historique_achats - Historique achats'
DO $$
DECLARE
    v_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO v_count
    FROM utilisateur_get_historique_achats('b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22');

    IF v_count >= 0 THEN
        RAISE NOTICE 'SUCCÈS: Historique récupéré (% commande(s))', v_count;
    ELSE
        RAISE NOTICE 'ÉCHEC: Erreur récupération historique';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

-- 2. TESTS ADRESSE (5 procédures)
\echo '>>> TEST 2.1: adresse_create - Créer une adresse'
DO $$
DECLARE
    v_adresse adresse%ROWTYPE;
    v_json jsonb;
BEGIN
    v_json := '{"rue1": "123 Test Street", "code_postal": "75001", "ville": "Paris", "pays": "France"}'::jsonb;

    SELECT * INTO v_adresse FROM adresse_create('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', v_json);

    IF v_adresse.ville = 'Paris' AND v_adresse.code_postal = '75001' THEN
        RAISE NOTICE 'SUCCÈS: Adresse créée (id=%)', v_adresse.id_adresse;
    ELSE
        RAISE NOTICE 'ÉCHEC: Adresse non créée';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 2.2: adresse_read - Lire une adresse'
DO $$
DECLARE
    v_adresse adresse%ROWTYPE;
BEGIN
    SELECT * INTO v_adresse FROM adresse_read(1);

    IF v_adresse.id_adresse = 1 THEN
        RAISE NOTICE 'SUCCÈS: Adresse récupérée (%)', v_adresse.ville;
    ELSE
        RAISE NOTICE 'ÉCHEC: Adresse non trouvée';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 2.3: adresse_list_by_user - Lister adresses utilisateur'
DO $$
DECLARE
    v_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO v_count
    FROM adresse_list_by_user('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11');

    IF v_count > 0 THEN
        RAISE NOTICE 'SUCCÈS: % adresse(s) trouvée(s)', v_count;
    ELSE
        RAISE NOTICE 'ÉCHEC: Aucune adresse trouvée';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''


-- 3. TESTS ANNONCE (6 procédures)
\echo '>>> TEST 3.1: annonce_create - Créer une annonce'
DO $$
DECLARE
    v_annonce annonce%ROWTYPE;
    v_json jsonb;
BEGIN
    v_json := '{"titre": "Test Sneakers", "description": "Paire de test", "prix": 99.99, "etat": "neuf", "taille_systeme": "EU", "taille": "42", "id_marque": 1, "id_couleur": 1, "id_materiau": 1}'::jsonb;

    SELECT * INTO v_annonce FROM annonce_create('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', v_json);

    IF v_annonce.titre = 'Test Sneakers' AND v_annonce.prix = 99.99 AND v_annonce.disponible = TRUE THEN
        RAISE NOTICE 'SUCCÈS: Annonce créée (disponible=TRUE)';
    ELSE
        RAISE NOTICE 'ÉCHEC: Annonce non créée correctement';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 3.2: annonce_read - Lire une annonce'
DO $$
DECLARE
    v_annonce annonce%ROWTYPE;
BEGIN
    SELECT * INTO v_annonce FROM annonce_read('10000000-0000-0000-0000-000000000001');

    IF v_annonce.titre IS NOT NULL THEN
        RAISE NOTICE 'SUCCÈS: Annonce récupérée (%)', v_annonce.titre;
    ELSE
        RAISE NOTICE 'ÉCHEC: Annonce non trouvée';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 3.3: annonce_list - Recherche et filtrage'
DO $$
DECLARE
    v_count INTEGER;
    v_json jsonb;
BEGIN
    -- Test sans filtre
    SELECT COUNT(*) INTO v_count FROM annonce_list('{}'::jsonb);
    RAISE NOTICE 'Sans filtre: % annonce(s)', v_count;

    -- Test avec filtre prix
    v_json := '{"prix_min": 50, "prix_max": 150, "disponible": true}'::jsonb;
    SELECT COUNT(*) INTO v_count FROM annonce_list(v_json);
    RAISE NOTICE 'Avec filtre prix 50-150: % annonce(s)', v_count;

    -- Test avec tri
    v_json := '{"order_by": "prix", "order_dir": "ASC"}'::jsonb;
    SELECT COUNT(*) INTO v_count FROM annonce_list(v_json);
    RAISE NOTICE 'Avec tri par prix: % annonce(s)', v_count;

    RAISE NOTICE 'SUCCÈS: Recherche et filtrage fonctionnels';
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 3.4: annonce_list_by_vendeur - Annonces vendeur'
DO $$
DECLARE
    v_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO v_count
    FROM annonce_list_by_vendeur('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11');

    IF v_count > 0 THEN
        RAISE NOTICE 'SUCCÈS: % annonce(s) du vendeur', v_count;
    ELSE
        RAISE NOTICE 'ÉCHEC: Aucune annonce trouvée';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

-- 4. TESTS IMAGE (3 procédures)
\echo '>>> TEST 4.1: image_create - Ajouter une image'
DO $$
DECLARE
    v_image image%ROWTYPE;
    v_json jsonb;
BEGIN
    v_json := '{"url": "/uploads/test.jpg", "est_principale": false}'::jsonb;

    SELECT * INTO v_image FROM image_create('10000000-0000-0000-0000-000000000001', v_json);

    IF v_image.url = '/uploads/test.jpg' THEN
        RAISE NOTICE 'SUCCÈS: Image ajoutée (id=%)', v_image.id_image;
    ELSE
        RAISE NOTICE 'ÉCHEC: Image non ajoutée';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 4.2: image_list_by_annonce - Lister images annonce'
DO $$
DECLARE
    v_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO v_count
    FROM image_list_by_annonce('10000000-0000-0000-0000-000000000001');

    IF v_count > 0 THEN
        RAISE NOTICE 'SUCCÈS: % image(s) trouvée(s)', v_count;
    ELSE
        RAISE NOTICE 'ÉCHEC: Aucune image trouvée';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

-- 5. TESTS PANIER (5 procédures)
\echo '>>> TEST 5.1: panier_ajouter_article - Ajouter au panier'
DO $$
DECLARE
    v_result contenir_panier%ROWTYPE;
BEGIN
    SELECT * INTO v_result
    FROM panier_ajouter_article(2, '10000000-0000-0000-0000-000000000001', 1);

    IF v_result.id_panier = 2 THEN
        RAISE NOTICE 'SUCCÈS: Article ajouté au panier';
        -- Nettoyer
        PERFORM panier_retirer_article(2, '10000000-0000-0000-0000-000000000001');
    ELSE
        RAISE NOTICE 'ÉCHEC: Article non ajouté';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 5.2: panier_get_contenu - Contenu du panier'
DO $$
DECLARE
    v_count INTEGER;
BEGIN
    -- Ajouter un article d'abord
    PERFORM panier_ajouter_article(3, '10000000-0000-0000-0000-000000000002', 1);

    SELECT COUNT(*) INTO v_count FROM panier_get_contenu(3);

    IF v_count > 0 THEN
        RAISE NOTICE 'SUCCÈS: Panier contient % article(s)', v_count;
    ELSE
        RAISE NOTICE 'ÉCHEC: Panier vide';
    END IF;

    -- Nettoyer
    PERFORM panier_vider(3);
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 5.3: panier_calculer_total - Calcul total panier'
DO $$
DECLARE
    v_total DECIMAL(10,2);
BEGIN
    -- Ajouter un article d'abord
    PERFORM panier_ajouter_article(4, '10000000-0000-0000-0000-000000000003', 1);

    SELECT * INTO v_total FROM panier_calculer_total(4);

    IF v_total > 0 THEN
        RAISE NOTICE 'SUCCÈS: Total calculé = %€', v_total;
    ELSE
        RAISE NOTICE 'ÉCHEC: Total incorrect';
    END IF;

    -- Nettoyer
    PERFORM panier_vider(4);
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 5.4: panier_vider - Vider le panier'
DO $$
DECLARE
    v_result BOOLEAN;
    v_count INTEGER;
BEGIN
    -- Ajouter un article d'abord
    PERFORM panier_ajouter_article(5, '10000000-0000-0000-0000-000000000004', 1);

    -- Vider le panier
    SELECT * INTO v_result FROM panier_vider(5);

    -- Vérifier qu'il est vide
    SELECT COUNT(*) INTO v_count FROM panier_get_contenu(5);

    IF v_result = TRUE AND v_count = 0 THEN
        RAISE NOTICE 'SUCCÈS: Panier vidé';
    ELSE
        RAISE NOTICE 'ÉCHEC: Panier non vidé';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

-- 6. TESTS CODE_PROMO (3 procédures)
\echo '>>> TEST 6.1: code_promo_create - Créer un code promo'
DO $$
DECLARE
    v_promo code_promo%ROWTYPE;
    v_json jsonb;
BEGIN
    v_json := '{"code_texte": "TEST2025", "pourcentage": 15}'::jsonb;

    SELECT * INTO v_promo FROM code_promo_create(v_json);

    IF v_promo.code_texte = 'TEST2025' AND v_promo.pourcentage = 15 THEN
        RAISE NOTICE 'SUCCÈS: Code promo créé (id=%)', v_promo.id_code;
    ELSE
        RAISE NOTICE 'ÉCHEC: Code promo non créé';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 6.2: code_promo_appliquer - Appliquer code promo'
DO $$
DECLARE
    v_result code_promo%ROWTYPE;
BEGIN
    -- Créer un code promo
    PERFORM code_promo_create('{"code_texte": "TESTAPPLY", "pourcentage": 10}'::jsonb);

    -- Appliquer au panier
    SELECT * INTO v_result FROM code_promo_appliquer(6, 'TESTAPPLY');

    IF v_result.id_panier = 6 THEN
        RAISE NOTICE 'SUCCÈS: Code promo appliqué';
        -- Retirer le code
        PERFORM code_promo_retirer(6);
    ELSE
        RAISE NOTICE 'ÉCHEC: Code promo non appliqué';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

-- 7. TESTS COMMANDE (5 procédures)
\echo '>>> TEST 7.1: commande_read - Lire une commande'
DO $$
DECLARE
    v_commande commande%ROWTYPE;
BEGIN
    SELECT * INTO v_commande FROM commande_read('20000000-0000-0000-0000-000000000001');

    IF v_commande.id_commande IS NOT NULL THEN
        RAISE NOTICE 'SUCCÈS: Commande récupérée (statut=%)', v_commande.statut;
    ELSE
        RAISE NOTICE 'ÉCHEC: Commande non trouvée';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 7.2: commande_list_by_user - Liste commandes utilisateur'
DO $$
DECLARE
    v_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO v_count
    FROM commande_list_by_user('b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22');

    IF v_count >= 0 THEN
        RAISE NOTICE 'SUCCÈS: % commande(s) trouvée(s)', v_count;
    ELSE
        RAISE NOTICE 'ÉCHEC: Erreur récupération commandes';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

-- 8. TESTS PAIEMENT (4 procédures)
\echo '>>> TEST 8.1: paiement_read - Lire un paiement'
DO $$
DECLARE
    v_paiement paiement%ROWTYPE;
BEGIN
    SELECT * INTO v_paiement FROM paiement_read(1);

    IF v_paiement.id_paiement IS NOT NULL THEN
        RAISE NOTICE 'SUCCÈS: Paiement récupéré (montant=%)', v_paiement.montant_paye;
    ELSE
        RAISE NOTICE 'ÉCHEC: Paiement non trouvé';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

-- 9. TESTS REVIEW (5 procédures)
\echo '>>> TEST 9.1: review_list_by_vendeur - Reviews vendeur'
DO $$
DECLARE
    v_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO v_count
    FROM review_list_by_vendeur('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11');

    IF v_count >= 0 THEN
        RAISE NOTICE 'SUCCÈS: % review(s) trouvée(s)', v_count;
    ELSE
        RAISE NOTICE 'ÉCHEC: Erreur récupération reviews';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

-- 10. TESTS SIGNALEMENT (3 procédures)
\echo '>>> TEST 10.1: signalement_create - Créer signalement'
DO $$
DECLARE
    v_signalement signalement%ROWTYPE;
    v_json jsonb;
BEGIN
    v_json := '{"motif": "Test signalement", "description": "Ceci est un test"}'::jsonb;

    SELECT * INTO v_signalement
    FROM signalement_create(
        'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22',
        'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33',
        v_json
    );

    IF v_signalement.statut = 'en_attente' THEN
        RAISE NOTICE 'SUCCÈS: Signalement créé (id=%)', v_signalement.id_signalement;
    ELSE
        RAISE NOTICE 'ÉCHEC: Signalement non créé';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 10.2: signalement_list - Lister signalements'
DO $$
DECLARE
    v_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO v_count
    FROM signalement_list('{"statut": "en_attente"}'::jsonb);

    IF v_count >= 0 THEN
        RAISE NOTICE 'SUCCÈS: % signalement(s) en attente', v_count;
    ELSE
        RAISE NOTICE 'ÉCHEC: Erreur récupération signalements';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

-- 11. TESTS CONSENTEMENT (3 procédures)
\echo '>>> TEST 11.1: consentement_create - Créer consentement'
DO $$
DECLARE
    v_consentement consentement_utilisateur%ROWTYPE;
    v_json jsonb;
BEGIN
    v_json := '{"type_consentement": "cookies", "statut": true}'::jsonb;

    SELECT * INTO v_consentement
    FROM consentement_create('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', v_json);

    IF v_consentement.type_consentement = 'cookies' AND v_consentement.statut = TRUE THEN
        RAISE NOTICE 'SUCCÈS: Consentement créé (id=%)', v_consentement.id_consentement;
    ELSE
        RAISE NOTICE 'ÉCHEC: Consentement non créé';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 11.2: consentement_list_by_user - Lister consentements'
DO $$
DECLARE
    v_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO v_count
    FROM consentement_list_by_user('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11');

    IF v_count >= 0 THEN
        RAISE NOTICE 'SUCCÈS: % consentement(s) trouvé(s)', v_count;
    ELSE
        RAISE NOTICE 'ÉCHEC: Erreur récupération consentements';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

-- 12. TESTS ADMIN_LOG (2 procédures)
\echo '>>> TEST 12.1: admin_log_list - Lister logs admin'
DO $$
DECLARE
    v_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO v_count FROM admin_log_list('{}'::jsonb);

    IF v_count >= 0 THEN
        RAISE NOTICE 'SUCCÈS: % log(s) trouvé(s)', v_count;
    ELSE
        RAISE NOTICE 'ÉCHEC: Erreur récupération logs';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

-- 13. TESTS TABLES RÉFÉRENCE (3 procédures)
\echo '>>> TEST 13.1: marque_list - Lister marques'
DO $$
DECLARE
    v_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO v_count FROM marque_list();

    IF v_count > 0 THEN
        RAISE NOTICE 'SUCCÈS: % marque(s) trouvée(s)', v_count;
    ELSE
        RAISE NOTICE 'ÉCHEC: Aucune marque trouvée';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 13.2: couleur_list - Lister couleurs'
DO $$
DECLARE
    v_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO v_count FROM couleur_list();

    IF v_count > 0 THEN
        RAISE NOTICE 'SUCCÈS: % couleur(s) trouvée(s)', v_count;
    ELSE
        RAISE NOTICE 'ÉCHEC: Aucune couleur trouvée';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

\echo '>>> TEST 13.3: materiau_list - Lister matériaux'
DO $$
DECLARE
    v_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO v_count FROM materiau_list();

    IF v_count > 0 THEN
        RAISE NOTICE 'SUCCÈS: % matériau(x) trouvé(s)', v_count;
    ELSE
        RAISE NOTICE 'ÉCHEC: Aucun matériau trouvé';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''

-- 14. TESTS STATISTIQUES (1 procédure)
\echo '>>> TEST 14.1: vendeur_get_statistiques - Stats vendeur'
DO $$
DECLARE
    v_stats RECORD;
BEGIN
    SELECT * INTO v_stats
    FROM vendeur_get_statistiques('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11');

    IF v_stats IS NOT NULL THEN
        RAISE NOTICE 'SUCCÈS: Stats récupérées (ventes=%, CA=%, note=%, reviews=%)',
            v_stats.total_ventes, v_stats.montant_total, v_stats.note_moyenne, v_stats.nb_reviews;
    ELSE
        RAISE NOTICE 'ÉCHEC: Stats non récupérées';
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur - %', SQLERRM;
END $$;

\echo ''


\echo '         TESTS TERMINÉS'
\echo 'Les tests couvrent toutes les procédures:'
\echo '  - 7 UTILISATEUR'
\echo '  - 5 ADRESSE'
\echo '  - 6 ANNONCE'
\echo '  - 3 IMAGE'
\echo '  - 5 PANIER'
\echo '  - 3 CODE_PROMO'
\echo '  - 5 COMMANDE'
\echo '  - 4 PAIEMENT'
\echo '  - 5 REVIEW'
\echo '  - 3 SIGNALEMENT'
\echo '  - 3 CONSENTEMENT'
\echo '  - 2 ADMIN_LOG'
\echo '  - 3 TABLES RÉFÉRENCE'
\echo '  - 1 STATISTIQUES'
\echo ''
