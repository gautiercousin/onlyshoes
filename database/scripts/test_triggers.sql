-- Ce script teste tous les triggers et affiche les résultats
-- Chaque test devrait échouer avec un code d'erreur SQLSTATE spécifique

-- TEST SA001: Annonce non disponible
\echo '>>> Test SA001: Annonce non disponible'
DO $$
BEGIN
    INSERT INTO LIGNE_PANIER (quantite) VALUES (1);
    INSERT INTO CONTENIR_PANIER (id_panier, id_annonce, id_ligne_panier)
    VALUES (2, '10000000-0000-0000-0000-000000000007', currval('ligne_panier_id_ligne_panier_seq'));

    RAISE NOTICE 'ÉCHEC: Le trigger n''a pas bloqué l''insertion';
EXCEPTION
    WHEN SQLSTATE 'SA001' THEN
        RAISE NOTICE 'SUCCÈS: SA001 - Annonce non disponible bloquée';
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur inattendue - %', SQLERRM;
END $$;

\echo ''

-- TEST SA003: Modification commande livrée
\echo '>>> Test SA003: Modification commande livrée'
DO $$
BEGIN
    UPDATE COMMANDE
    SET statut = 'annulee'
    WHERE id_commande = '20000000-0000-0000-0000-000000000001';

    RAISE NOTICE 'ÉCHEC: Le trigger n''a pas bloqué la modification';
EXCEPTION
    WHEN SQLSTATE 'SA003' THEN
        RAISE NOTICE 'SUCCÈS: SA003 - Modification commande livrée bloquée';
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur inattendue - %', SQLERRM;
END $$;

\echo ''

-- TEST SA004: Suppression commande livrée
\echo '>>> Test SA004: Suppression commande livrée'
DO $$
BEGIN
    DELETE FROM COMMANDE
    WHERE id_commande = '20000000-0000-0000-0000-000000000001';

    RAISE NOTICE 'ÉCHEC: Le trigger n''a pas bloqué la suppression';
EXCEPTION
    WHEN SQLSTATE 'SA004' THEN
        RAISE NOTICE 'SUCCÈS: SA004 - Suppression commande livrée bloquée';
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur inattendue - %', SQLERRM;
END $$;

\echo ''

-- TEST SA006: Vendeur achète sa propre annonce
\echo '>>> Test SA006: Vendeur achète sa propre annonce'
DO $$
BEGIN
    INSERT INTO LIGNE_PANIER (quantite) VALUES (1);
    INSERT INTO CONTENIR_PANIER (id_panier, id_annonce, id_ligne_panier)
    VALUES (1, '10000000-0000-0000-0000-000000000001', currval('ligne_panier_id_ligne_panier_seq'));

    RAISE NOTICE 'ÉCHEC: Le trigger n''a pas bloqué l''auto-achat';
EXCEPTION
    WHEN SQLSTATE 'SA006' THEN
        RAISE NOTICE 'SUCCÈS: SA006 - Auto-achat bloqué';
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur inattendue - %', SQLERRM;
END $$;

\echo ''

-- TEST SA010: Review sans achat
\echo '>>> Test SA010: Review sans achat'
DO $$
BEGIN
    INSERT INTO REVIEW (note, commentaire, id_utilisateur_auteur, id_utilisateur_vendeur)
    VALUES (5, 'Super vendeur!', 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44', 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33');

    RAISE NOTICE 'ÉCHEC: Le trigger n''a pas bloqué la review';
EXCEPTION
    WHEN SQLSTATE 'SA010' THEN
        RAISE NOTICE 'SUCCÈS: SA010 - Review sans achat bloquée';
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur inattendue - %', SQLERRM;
END $$;

\echo ''

-- TEST SA012: Auto-signalement
\echo '>>> Test SA012: Auto-signalement'
DO $$
BEGIN
    INSERT INTO SIGNALEMENT (motif, description, statut, id_utilisateur_auteur, id_utilisateur_cible)
    VALUES ('Test', 'Je me signale moi-même', 'en_attente',
            'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22',
            'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22');

    RAISE NOTICE 'ÉCHEC: Le trigger n''a pas bloqué l''auto-signalement';
EXCEPTION
    WHEN SQLSTATE 'SA012' THEN
        RAISE NOTICE 'SUCCÈS: SA012 - Auto-signalement bloqué';
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur inattendue - %', SQLERRM;
END $$;

\echo ''

-- TEST SA013: Commande avec paiement non validé
\echo '>>> Test SA013: Commande avec paiement non validé'
DO $$
DECLARE
    v_paiement_id INTEGER;
BEGIN
    INSERT INTO PAIEMENT (type, statut, montant_paye)
    VALUES ('carte_bancaire', 'en_attente', 203.99)
    RETURNING id_paiement INTO v_paiement_id;

    INSERT INTO COMMANDE (id_utilisateur, id_paiement, statut)
    VALUES ('b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22', v_paiement_id, 'en_preparation');

    RAISE NOTICE 'ÉCHEC: Le trigger n''a pas bloqué la commande';
EXCEPTION
    WHEN SQLSTATE 'SA013' THEN
        RAISE NOTICE 'SUCCÈS: SA013 - Commande avec paiement invalide bloquée';
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur inattendue - %', SQLERRM;
END $$;

\echo ''

-- TEST SA014: Montant paiement incorrect
\echo '>>> Test SA014: Montant paiement incorrect'
DO $$
BEGIN
    INSERT INTO PAIEMENT (type, statut, montant_paye)
    VALUES ('carte_bancaire', 'valide', 1.00);

    RAISE NOTICE 'ÉCHEC: Le trigger n''a pas bloqué le paiement';
EXCEPTION
    WHEN SQLSTATE 'SA014' THEN
        RAISE NOTICE 'SUCCÈS: SA014 - Montant incorrect bloqué';
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur inattendue - %', SQLERRM;
END $$;

\echo ''

-- TEST SA015: Mot de passe non haché
\echo '>>> Test SA015: Mot de passe non haché'
DO $$
BEGIN
    INSERT INTO UTILISATEUR (nom, prenom, email, mdp, type_compte)
    VALUES ('Test', 'User', 'test@test.fr', 'password123', 'standard');

    RAISE NOTICE 'ÉCHEC: Le trigger n''a pas bloqué le mot de passe en clair';
EXCEPTION
    WHEN SQLSTATE 'SA015' THEN
        RAISE NOTICE 'SUCCÈS: SA015 - Mot de passe non haché bloqué';
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur inattendue - %', SQLERRM;
END $$;

\echo ''

--  ++STEST BONUS: Vérifier qu'un hash valide fonctionne
\echo '>>> Test BONUS: Hash bcrypt valide'
DO $$
BEGIN
    -- Mot de passe = 'password'
    INSERT INTO UTILISATEUR (nom, prenom, email, mdp, type_compte)
    VALUES ('Test', 'User', 'testvalid@test.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'standard');

    RAISE NOTICE 'SUCCÈS: Hash bcrypt accepté correctement';
EXCEPTION
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Hash valide rejeté - %', SQLERRM;
END $$;

\echo ''

-- TEST SA016: Action admin par utilisateur non-admin
\echo '>>> Test SA016: Action admin par utilisateur non-admin'
DO $$
BEGIN
    INSERT INTO ADMIN_LOG (action_type, id_cible, raison, ip_address, id_utilisateur)
    VALUES ('bannir_utilisateur', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'Test', '10.0.0.1', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22');

    RAISE NOTICE 'ÉCHEC: Le trigger n''a pas bloqué l''action admin par un non-admin';
EXCEPTION
    WHEN SQLSTATE 'SA016' THEN
        RAISE NOTICE 'SUCCÈS: SA016 - Action admin par non-admin bloquée';
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur inattendue - %', SQLERRM;
END $$;

\echo ''

-- TEST SA017: Email invalide
\echo '>>> Test SA017: Email invalide'
DO $$
BEGIN
    -- Mot de passe = 'password' (le trigger devrait bloquer l'email, pas le mdp)
    INSERT INTO UTILISATEUR (nom, prenom, email, mdp, type_compte)
    VALUES ('Test', 'Email', 'email_invalide', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'standard');

    RAISE NOTICE 'ÉCHEC: Le trigger n''a pas bloqué l''email invalide';
EXCEPTION
    WHEN SQLSTATE 'SA017' THEN
        RAISE NOTICE 'SUCCÈS: SA017 - Email invalide bloqué';
    WHEN OTHERS THEN
        RAISE NOTICE 'ÉCHEC: Erreur inattendue - %', SQLERRM;
END $$;