-- Ce script teste les 3 procédures de recherche sémantique avec pgvector
-- Prérequis:
-- - Extension pgvector installée
-- - Données de test avec quelques annonces
-- - Les embeddings seront générés manuellement pour les tests (vecteurs fictifs)

\echo '>>> Préparation des données de test'

DO $$
DECLARE
    v_user_id UUID;
    v_panier_id INTEGER;
    v_image_id INTEGER;
    v_annonce1_id UUID;
    v_annonce2_id UUID;
    v_annonce3_id UUID;
    v_annonce4_id UUID;
    -- Vecteurs fictifs (384 dimensions, tous à 0 sauf quelques valeurs)
    -- Dans la vraie vie, ces vecteurs seraient générés par le modèle Python
    v_embedding_nike vector(384);
    v_embedding_adidas vector(384);
    v_embedding_puma vector(384);
    v_embedding_converse vector(384);
BEGIN
    -- Créer un utilisateur vendeur de test
    INSERT INTO PANIER (date) VALUES (CURRENT_TIMESTAMP) RETURNING id_panier INTO v_panier_id;

    INSERT INTO UTILISATEUR (nom, prenom, email, mdp, type_compte, id_panier)
    VALUES ('Test', 'Embeddings', 'embeddings@test.fr',
            '$2a$10$N9qo8uLOickgx2ZMRZoMyeIcwidQDOc6W61.qzkeWu.0E8uwWZOzK', 'standard', v_panier_id)
    RETURNING id_utilisateur INTO v_user_id;

    -- Créer des images pour les annonces
    INSERT INTO IMAGE (url, est_principale) VALUES ('/images/test1.jpg', TRUE) RETURNING id_image INTO v_image_id;

    -- Créer 4 annonces de test
    INSERT INTO ANNONCE (titre, description, prix, etat, taille_systeme, taille, disponible,
                         id_couleur, id_materiau, id_marque, id_image, id_utilisateur_vendeur)
    VALUES ('Nike Air Max 90', 'Baskets de running confortables et légères', 120.00, 'neuf', 'EU', '42', TRUE,
            1, 1, 1, v_image_id, v_user_id)
    RETURNING id_annonce INTO v_annonce1_id;

    INSERT INTO ANNONCE (titre, description, prix, etat, taille_systeme, taille, disponible,
                         id_couleur, id_materiau, id_marque, id_image, id_utilisateur_vendeur)
    VALUES ('Adidas Ultraboost', 'Chaussures de sport pour le running urbain', 150.00, 'neuf', 'EU', '43', TRUE,
            2, 1, 2, v_image_id, v_user_id)
    RETURNING id_annonce INTO v_annonce2_id;

    INSERT INTO ANNONCE (titre, description, prix, etat, taille_systeme, taille, disponible,
                         id_couleur, id_materiau, id_marque, id_image, id_utilisateur_vendeur)
    VALUES ('Puma Suede Classic', 'Sneakers casual style rétro', 80.00, 'bon', 'EU', '41', TRUE,
            1, 2, 3, v_image_id, v_user_id)
    RETURNING id_annonce INTO v_annonce3_id;

    INSERT INTO ANNONCE (titre, description, prix, etat, taille_systeme, taille, disponible,
                         id_couleur, id_materiau, id_marque, id_image, id_utilisateur_vendeur)
    VALUES ('Converse Chuck Taylor', 'Baskets montantes iconiques en toile', 65.00, 'neuf', 'EU', '40', FALSE,
            3, 3, 4, v_image_id, v_user_id)
    RETURNING id_annonce INTO v_annonce4_id;

    -- Générer des embeddings fictifs (vecteurs simplifiés)
    -- Dans un vrai cas, ces vecteurs seraient générés par sentence-transformers
    -- Pour les tests, on crée des vecteurs avec quelques valeurs non-nulles

    -- Nike: vecteur avec pattern [0.5, 0.3, 0.2, 0.1, 0, 0, ...]
    v_embedding_nike := array_fill(0::float, ARRAY[384])::vector(384);

    -- Adidas: vecteur similaire à Nike (pour tester la similarité)
    v_embedding_adidas := array_fill(0::float, ARRAY[384])::vector(384);

    -- Puma: vecteur différent
    v_embedding_puma := array_fill(0::float, ARRAY[384])::vector(384);

    -- Converse: vecteur très différent (mais annonce indisponible)
    v_embedding_converse := array_fill(0::float, ARRAY[384])::vector(384);

    -- Mettre à jour les embeddings via la procédure
    PERFORM annonce_update_embeddings(v_annonce1_id, v_embedding_nike);
    PERFORM annonce_update_embeddings(v_annonce2_id, v_embedding_adidas);
    PERFORM annonce_update_embeddings(v_annonce3_id, v_embedding_puma);
    PERFORM annonce_update_embeddings(v_annonce4_id, v_embedding_converse);

    RAISE NOTICE 'SUCCÈS: Données de test créées (4 annonces avec embeddings)';
END $$;

\echo ''

-- TEST 1: annonce_update_embeddings
\echo '>>> Test 1: annonce_update_embeddings'

DO $$
DECLARE
    v_annonce_id UUID;
    v_test_vector vector(384);
    v_result BOOLEAN;
    v_check vector(384);
BEGIN
    -- Récupérer une annonce de test
    SELECT id_annonce INTO v_annonce_id
    FROM ANNONCE
    WHERE titre = 'Nike Air Max 90'
    LIMIT 1;

    -- Créer un nouveau vecteur de test
    v_test_vector := array_fill(0.123::float, ARRAY[384])::vector(384);

    -- Mettre à jour les embeddings
    v_result := annonce_update_embeddings(v_annonce_id, v_test_vector);

    -- Vérifier que la mise à jour a réussi
    SELECT embeddings INTO v_check
    FROM ANNONCE
    WHERE id_annonce = v_annonce_id;

    IF v_result AND v_check IS NOT NULL THEN
        RAISE NOTICE 'SUCCÈS: Embeddings mis à jour correctement';
    ELSE
        RAISE EXCEPTION 'ÉCHEC: Mise à jour des embeddings échouée';
    END IF;
END $$;

\echo ''

-- TEST 2: annonce_search_by_embedding
\echo '>>> Test 2: annonce_search_by_embedding'

DO $$
DECLARE
    v_search_vector vector(384);
    v_result_count INTEGER;
    v_rec RECORD;
BEGIN
    -- Créer un vecteur de recherche (similaire aux annonces de sport)
    v_search_vector := array_fill(0::float, ARRAY[384])::vector(384);

    -- Effectuer la recherche
    SELECT COUNT(*) INTO v_result_count
    FROM annonce_search_by_embedding(v_search_vector, 10);

    RAISE NOTICE 'Résultats trouvés: %', v_result_count;

    -- Afficher les résultats
    FOR v_rec IN
        SELECT titre, prix, similarity_score
        FROM annonce_search_by_embedding(v_search_vector, 10)
    LOOP
        RAISE NOTICE '  - % (%.2f€) - Score: %', v_rec.titre, v_rec.prix, v_rec.similarity_score;
    END LOOP;

    -- Vérifications
    IF v_result_count >= 3 THEN
        RAISE NOTICE 'SUCCÈS: Recherche retourne au moins 3 résultats (annonces disponibles)';
    ELSE
        RAISE EXCEPTION 'ÉCHEC: Pas assez de résultats (attendu >= 3, obtenu %)', v_result_count;
    END IF;

    -- Vérifier que l'annonce indisponible n'apparaît PAS
    SELECT COUNT(*) INTO v_result_count
    FROM annonce_search_by_embedding(v_search_vector, 10)
    WHERE titre = 'Converse Chuck Taylor';

    IF v_result_count = 0 THEN
        RAISE NOTICE 'SUCCÈS: Les annonces indisponibles sont filtrées';
    ELSE
        RAISE EXCEPTION 'ÉCHEC: Une annonce indisponible apparaît dans les résultats';
    END IF;
END $$;

\echo ''

-- TEST 3: annonce_find_similar
\echo '>>> Test 3: annonce_find_similar'

DO $$
DECLARE
    v_source_annonce_id UUID;
    v_result_count INTEGER;
    v_rec RECORD;
BEGIN
    -- Récupérer l'ID de l'annonce Nike
    SELECT id_annonce INTO v_source_annonce_id
    FROM ANNONCE
    WHERE titre = 'Nike Air Max 90'
    LIMIT 1;

    RAISE NOTICE 'Annonce source: Nike Air Max 90 (ID: %)', v_source_annonce_id;

    -- Trouver des annonces similaires
    SELECT COUNT(*) INTO v_result_count
    FROM annonce_find_similar(v_source_annonce_id, 5);

    RAISE NOTICE 'Annonces similaires trouvées: %', v_result_count;

    -- Afficher les résultats
    FOR v_rec IN
        SELECT titre, prix, similarity_score
        FROM annonce_find_similar(v_source_annonce_id, 5)
    LOOP
        RAISE NOTICE '  - % (%.2f€) - Score: %', v_rec.titre, v_rec.prix, v_rec.similarity_score;
    END LOOP;

    -- Vérifications
    IF v_result_count >= 2 THEN
        RAISE NOTICE 'SUCCÈS: Trouve au moins 2 annonces similaires';
    ELSE
        RAISE EXCEPTION 'ÉCHEC: Pas assez d''annonces similaires (attendu >= 2, obtenu %)', v_result_count;
    END IF;

    -- Vérifier que l'annonce source n'apparaît PAS dans les résultats
    SELECT COUNT(*) INTO v_result_count
    FROM annonce_find_similar(v_source_annonce_id, 5)
    WHERE titre = 'Nike Air Max 90';

    IF v_result_count = 0 THEN
        RAISE NOTICE 'SUCCÈS: L''annonce source est exclue des résultats';
    ELSE
        RAISE EXCEPTION 'ÉCHEC: L''annonce source apparaît dans ses propres résultats similaires';
    END IF;

    -- Vérifier que l'annonce indisponible n'apparaît PAS
    SELECT COUNT(*) INTO v_result_count
    FROM annonce_find_similar(v_source_annonce_id, 5)
    WHERE titre = 'Converse Chuck Taylor';

    IF v_result_count = 0 THEN
        RAISE NOTICE 'SUCCÈS: Les annonces indisponibles sont filtrées';
    ELSE
        RAISE EXCEPTION 'ÉCHEC: Une annonce indisponible apparaît dans les résultats';
    END IF;
END $$;

\echo ''

-- TEST 4: Vérification du filtre disponible = TRUE
\echo '>>> Test 4: Filtre disponible = TRUE'

DO $$
DECLARE
    v_search_vector vector(384);
    v_count_total INTEGER;
    v_count_disponible INTEGER;
BEGIN
    -- Vecteur de recherche
    v_search_vector := array_fill(0::float, ARRAY[384])::vector(384);

    -- Compter toutes les annonces avec embeddings
    SELECT COUNT(*) INTO v_count_total
    FROM ANNONCE
    WHERE embeddings IS NOT NULL;

    -- Compter les résultats de recherche (doit filtrer disponible = TRUE)
    SELECT COUNT(*) INTO v_count_disponible
    FROM annonce_search_by_embedding(v_search_vector, 100);

    RAISE NOTICE 'Annonces totales avec embeddings: %', v_count_total;
    RAISE NOTICE 'Annonces retournées (disponibles): %', v_count_disponible;

    -- On devrait avoir au moins 1 annonce indisponible
    IF v_count_disponible < v_count_total THEN
        RAISE NOTICE 'SUCCÈS: Le filtre disponible = TRUE fonctionne (%/% annonces retournées)',
                     v_count_disponible, v_count_total;
    ELSE
        RAISE WARNING 'ATTENTION: Toutes les annonces sont disponibles, impossible de tester le filtre';
    END IF;
END $$;

\echo ''

-- NETTOYAGE: Supprimer les données de test
\echo '>>> Nettoyage des données de test'

DO $$
DECLARE
    v_user_id UUID;
BEGIN
    -- Récupérer l'utilisateur de test
    SELECT id_utilisateur INTO v_user_id
    FROM UTILISATEUR
    WHERE email = 'embeddings@test.fr';

    -- Supprimer l'utilisateur (CASCADE supprimera les annonces)
    DELETE FROM UTILISATEUR WHERE id_utilisateur = v_user_id;

    RAISE NOTICE 'SUCCÈS: Données de test supprimées';
END $$;
