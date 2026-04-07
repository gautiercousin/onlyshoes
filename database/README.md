# Base de Données - OnlyShoes

## Codes d'erreur (SQLSTATE)

| SQLSTATE | Description | Trigger/Procedure |
|----------|-------------|-------------------|
| SA003 | Modification d'une commande finalisée interdite | `trg_verif_modification_commande` |
| SA004 | Suppression d'une commande livrée interdite | `trg_verif_suppression_commande` |
| SA006 | Vendeur ne peut pas acheter sa propre annonce | `trg_verif_vendeur_acheteur` |
| SA010 | Review autorisée uniquement après achat | `trg_verif_review_autorisee` |
| SA012 | Auto-signalement interdit (type user) | `trg_verif_signalement_integrite` |
| SA013 | Paiement refusé - impossible de créer une commande | `trg_verif_paiement_valide` |
| SA015 | Mot de passe non haché détecté - sécurité compromise | `trg_verif_password_hash` |
| SA016 | Action admin uniquement pour les administrateurs | `trg_verif_admin_action` |
| SA017 | Format d'email invalide | `trg_verif_email_format` |
| SA018 | Bannissement déjà appliqué interdit | `trg_verif_modification_status` |
| SA019 | Suspension déjà appliquée interdite | `trg_verif_modification_status` |
| SA020 | Bannir/suspendre un administrateur interdit | `trg_verif_modification_status` |
| SA023 | Un seul avis autorisé par couple acheteur-vendeur | `review_create()` |
| SA024 | Review nécessite au moins un achat chez ce vendeur | `review_create()` |

---


## Tests manuels des triggers

Ces tests se reposent sur les données par défaut insérées lors de l'exécution de `04-data.sql`.

### Test SA003
**Trigger**: `trg_verif_modification_commande`
**Attendu**: Erreur SA003 - Impossible de modifier une commande livrée

```sql
-- Tenter de modifier le statut d'une commande déjà livrée
UPDATE COMMANDE
SET statut = 'annulee'
WHERE id_commande = '20000000-0000-0000-0000-000000000001';
```

---

### Test SA004
**Trigger**: `trg_verif_suppression_commande`
**Attendu**: Erreur SA004 - Impossible de supprimer une commande livrée

```sql
-- Tenter de supprimer une commande livrée
DELETE FROM COMMANDE
WHERE id_commande = '20000000-0000-0000-0000-000000000001';
```

---

### Test SA006
**Trigger**: `trg_verif_vendeur_acheteur`
**Attendu**: Erreur SA006 - Le vendeur ne peut pas acheter sa propre annonce

```sql
-- Marie Dupont (id: a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11) tente d'acheter sa propre annonce
-- Elle est la vendeuse de l'annonce 10000000-0000-0000-0000-000000000001
SELECT commande_create(
    'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'::uuid,
    '[{"id_annonce": "10000000-0000-0000-0000-000000000001", "quantite": 1}]'::jsonb,
    89.99
);
```

---

### Test SA010
**Trigger**: `trg_verif_review_autorisee`
**Attendu**: Erreur SA010 - Review possible uniquement après achat

```sql
-- Lucas (acheteur) tente de laisser un avis à Sophie (vendeuse) sans avoir acheté chez elle
INSERT INTO REVIEW (note, commentaire, id_utilisateur_auteur, id_utilisateur_vendeur)
VALUES (5, 'Super vendeur!', 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44', 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33');
```

---

### Test SA012
**Trigger**: `trg_verif_signalement_integrite`
**Attendu**: Erreur SA012 - Auto-signalement interdit (type user)

```sql
-- Thomas tente de se signaler lui-même (type user)
INSERT INTO SIGNALEMENT (motif, description, statut, type, id_cible, id_utilisateur_auteur)
VALUES ('Test', 'Je me signale moi-même', 'en_attente',
  'user', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22');
```

---

### Test SA013
**Trigger**: `trg_verif_paiement_valide`
**Attendu**: Erreur SA013 - Commande impossible sans paiement validé

```sql
-- Créer un paiement en attente puis tenter de créer une commande
INSERT INTO PAIEMENT (type, statut, montant_paye)
VALUES ('carte_bancaire', 'en_attente', 100.00);

INSERT INTO COMMANDE (id_utilisateur, id_paiement, statut)
VALUES ('b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22', currval('paiement_id_paiement_seq'), 'en_preparation');
```

---

### Test SA015
**Trigger**: `trg_verif_password_hash`
**Attendu**: Erreur SA015 - Mot de passe en clair détecté (non haché)

```sql
-- Tenter d'insérer un utilisateur avec un mot de passe en clair
INSERT INTO UTILISATEUR (nom, prenom, email, mdp, type_compte)
VALUES ('Test', 'User', 'test@test.fr', 'password123', 'standard');

-- Test avec un hash valide (devrait fonctionner)
INSERT INTO UTILISATEUR (nom, prenom, email, mdp, type_compte)
VALUES ('Test', 'User2', 'test2@test.fr', '$2a$10$N9qo8uLOickgx2ZMRZoMye/Po0KXvHm.aCO5YJu2bHvvqK0n4r0m2', 'standard');
```

---

### Test SA016
**Trigger**: `trg_verif_admin_action`
**Attendu**: Erreur SA016 - Seuls les administrateurs peuvent effectuer des actions admin

```sql
-- Thomas (utilisateur standard) tente d'effectuer une action admin
INSERT INTO ADMIN_LOG (action_type, id_cible, raison, ip_address, id_utilisateur)
VALUES ('bannir_utilisateur', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'Test', '10.0.0.1', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22');
```

---

### Test SA017
**Trigger**: `trg_verif_email_format`
**Attendu**: Erreur SA017 - Format d'email invalide

```sql
-- Tenter d'insérer un utilisateur avec un email invalide
INSERT INTO UTILISATEUR (nom, prenom, email, mdp, type_compte)
VALUES ('Test', 'User', 'email_invalide', '$2a$10$N9qo8uLOickgx2ZMRZoMye/Po0KXvHm.aCO5YJu2bHvvqK0n4r0m2', 'standard');

-- Test avec un email sans @
INSERT INTO UTILISATEUR (nom, prenom, email, mdp, type_compte)
VALUES ('Test', 'User', 'emailinvalide.com', '$2a$10$N9qo8uLOickgx2ZMRZoMye/Po0KXvHm.aCO5YJu2bHvvqK0n4r0m2', 'standard');

-- Test avec un email sans domaine
INSERT INTO UTILISATEUR (nom, prenom, email, mdp, type_compte)
VALUES ('Test', 'User', 'test@', '$2a$10$N9qo8uLOickgx2ZMRZoMye/Po0KXvHm.aCO5YJu2bHvvqK0n4r0m2', 'standard');

-- Test avec un email valide (devrait fonctionner)
INSERT INTO UTILISATEUR (nom, prenom, email, mdp, type_compte)
VALUES ('Test', 'User', 'test@example.com', '$2a$10$N9qo8uLOickgx2ZMRZoMye/Po0KXvHm.aCO5YJu2bHvvqK0n4r0m2', 'standard');
```

---

### Test SA018
**Trigger**: `trg_verif_modification_status`
**Attendu**: Erreur SA018 - Impossible de bannir un utilisateur déjà banni

```sql
-- Créer un utilisateur banni puis tenter de le rebannir
INSERT INTO UTILISATEUR (nom, prenom, email, mdp, type_compte, status)
VALUES ('Test', 'Bannis', 'bannis@test.fr', '$2a$10$N9qo8uLOickgx2ZMRZoMye/Po0KXvHm.aCO5YJu2bHvvqK0n4r0m2', 'standard', 'bannis');

UPDATE UTILISATEUR
SET status = 'bannis'
WHERE email = 'bannis@test.fr';
```

---

### Test SA019
**Trigger**: `trg_verif_modification_status`
**Attendu**: Erreur SA019 - Impossible de suspendre un utilisateur déjà suspendu

```sql
-- Créer un utilisateur suspendu puis tenter de le resuspendre
INSERT INTO UTILISATEUR (nom, prenom, email, mdp, type_compte, status)
VALUES ('Test', 'Suspendu', 'suspendu@test.fr', '$2a$10$N9qo8uLOickgx2ZMRZoMye/Po0KXvHm.aCO5YJu2bHvvqK0n4r0m2', 'standard', 'suspendu');

UPDATE UTILISATEUR
SET status = 'suspendu'
WHERE email = 'suspendu@test.fr';
```

---

### Test SA020
**Trigger**: `trg_verif_modification_status`
**Attendu**: Erreur SA020 - Impossible de bannir ou suspendre un administrateur

```sql
-- Tenter de bannir un admin
UPDATE UTILISATEUR
SET status = 'bannis'
WHERE email = 'admin@sae.local';
```

---

### Test SA023
**Procédure**: `review_create()`
**Attendu**: Erreur SA023 - Un seul avis autorisé par couple acheteur-vendeur

```sql
-- Thomas (b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22) a déjà laissé un avis à Marie
-- Tenter d'en laisser un second
SELECT review_create(
    'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'::uuid,
    'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'::uuid,
    '{"note": 5, "commentaire": "Deuxième avis"}'::jsonb
);
```

---

### Test SA024
**Procédure**: `review_create()`
**Attendu**: Erreur SA024 - Review nécessite au moins un achat chez ce vendeur

```sql
-- Lucas (d0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44) n'a jamais acheté chez Sophie (c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33)
-- Tenter de laisser un avis
SELECT review_create(
    'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44'::uuid,
    'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33'::uuid,
    '{"note": 5, "commentaire": "Super vendeur!"}'::jsonb
);
```

---

## Test automatisé des triggers

### Script automatisé

```bash
# Via docker.sh (recommandé)
./docker.sh test-triggers
```

Le script teste automatiquement les 9 triggers + 1 test bonus (hash bcrypt valide).

Les tests utilisent des blocs DO avec gestion d'exceptions - **les modifications sont automatiquement annulées en cas d'erreur**.

---

## Conformité GDPR

### Tables de conformité

**ADMIN_LOG** - Traçabilité des actions administrateurs (GDPR Article 30)
- Types d'actions: bannir_utilisateur, suspendre_utilisateur, supprimer_annonce, resoudre_signalement, rejeter_signalement, restaurer_utilisateur, modifier_commande
- Horodatage automatique + IP de l'admin
- Trigger SA016: seuls les administrateurs peuvent créer des logs

**CONSENTEMENT_UTILISATEUR** - Gestion des consentements utilisateurs (GDPR Article 7)
- Types: cookies, conditions_utilisation, traitement_donnees, marketing
- Traçabilité date de consentement + date de retrait
- CASCADE DELETE lors de la suppression d'un utilisateur

---

## Sécurité: Mots de passe

### ⚠️ IMPORTANT: Hashing obligatoire

Les mots de passe **DOIVENT** être hachés avec bcrypt **AVANT** insertion dans la base.

Le trigger `trg_verif_password_hash` (SA015) bloque tout mot de passe non haché pour éviter les fuites en clair.

Le hash bcrypt est au format: `$2a$10$[22 chars salt][31 chars hash]` (60 caractères total).

---

## Procédures Stockées (Stored Procedures)
ucune requête SQL brute dans le code PHP. Toutes les opérations passent par des procédures utilisant le format JSONB pour les paramètres.

**Convention de nommage**: `{table}_{operation}` (ex: `utilisateur_create`, `annonce_list`)

---

### 1. UTILISATEUR (7 procédures)

#### `utilisateur_create(p_data jsonb)` -> utilisateur
**Exigence 6**: Créer un compte utilisateur

**Paramètres**:
```json
{
  "nom": "string",
  "prenom": "string",
  "email": "string",
  "mdp": "string (hash bcrypt)",
  "type_compte": "standard|admin (optionnel, défaut: standard)",
  "status": "actif|suspendu|bannis (optionnel, défaut: actif)"
}
```

**Retour**: Ligne complète de l'utilisateur créé

**Triggers activés**: SA015 (mot de passe haché), SA017 (format email)

---

#### `utilisateur_read(p_id uuid)` -> utilisateur
**Exigence 7**: Consulter un compte utilisateur

**Paramètres**: `p_id` - UUID de l'utilisateur

**Retour**: Ligne complète de l'utilisateur ou NULL

---

#### `utilisateur_update(p_id uuid, p_data jsonb)` -> utilisateur
**Exigence 8**: Modifier les informations d'un compte

**Paramètres**:
```json
{
  "nom": "string (optionnel)",
  "prenom": "string (optionnel)",
  "email": "string (optionnel)",
  "mdp": "string hash bcrypt (optionnel)",
  "status": "actif|suspendu|bannis (optionnel)"
}
```

**Retour**: Ligne mise à jour

**Triggers activés**: SA015 (si mdp modifié), SA017 (si email modifié), SA018-SA020 (si status modifié)

---

#### `utilisateur_delete(p_id uuid)` -> boolean
**Exigence 10**: Supprimer un compte (GDPR)

**Paramètres**: `p_id` - UUID de l'utilisateur

**Retour**: TRUE si suppression réussie

**Cascade**: Supprime également adresses, consentements

---

#### `utilisateur_login(p_email text, p_password text)` -> utilisateur
**Exigence 7**: Connexion utilisateur

**Paramètres**: Email et mot de passe en clair

**Retour**: Ligne utilisateur si email existe (PHP doit vérifier le hash avec `password_verify()`)

**Note**: La procédure ne vérifie PAS le mot de passe - c'est le rôle de PHP avec bcrypt. Elle bloque les comptes `bannis` et `suspendu`.

---

#### `utilisateur_list(p_filters jsonb)` -> SETOF utilisateur
**Exigence 28**: Lister les utilisateurs (admin)

**Paramètres**:
```json
{
  "type_compte": "standard|admin (optionnel)",
  "status": "actif|suspendu|bannis (optionnel)",
  "search": "string (recherche nom/prenom/email)"
}
```

**Retour**: Ensemble d'utilisateurs correspondants

---

#### `utilisateur_get_historique_achats(p_id uuid)` -> TABLE(...)
**Exigence 16**: Consulter l'historique des achats

**Paramètres**: `p_id` - UUID de l'utilisateur

**Retour**: Table avec colonnes:
- id_commande, date, statut, montant_total
- Pour chaque commande dans l'historique

---

### 2. ADRESSE (5 procédures)

#### `adresse_create(p_id_utilisateur uuid, p_data jsonb)` -> adresse
**Exigence 9**: Ajouter une adresse

**Paramètres**:
```json
{
  "rue1": "string",
  "rue2": "string (optionnel)",
  "code_postal": "string",
  "ville": "string",
  "pays": "string"
}
```

---

#### `adresse_read(p_id integer)` -> adresse
Consulter une adresse spécifique

---

#### `adresse_update(p_id integer, p_data jsonb)` -> adresse
**Exigence 9**: Modifier une adresse

---

#### `adresse_delete(p_id integer)` -> boolean
**Exigence 9**: Supprimer une adresse

---

#### `adresse_list_by_user(p_id_utilisateur uuid)` -> SETOF adresse
**Exigence 9**: Lister toutes les adresses d'un utilisateur

---

### 3. ANNONCE (6 procédures)

#### `annonce_create(p_id_vendeur uuid, p_data jsonb)` -> annonce
**Exigence 20**: Publier une annonce

**Paramètres**:
```json
{
  "titre": "string",
  "description": "string",
  "prix": "decimal",
  "etat": "neuf|comme_neuf|bon_etat|usage_visible|pour_pieces",
  "taille_systeme": "EU|UK|US",
  "taille": "string",
  "id_marque": "integer",
  "id_couleur": "integer",
  "id_materiau": "integer"
}
```

**Note**: disponible = TRUE par défaut

---

#### `annonce_read(p_id uuid)` -> annonce
**Exigence 1**: Consulter les détails d'une annonce

---

#### `annonce_update(p_id uuid, p_data jsonb)` -> annonce
**Exigence 21**: Modifier une annonce

**Paramètres**: Mêmes champs que create (tous optionnels)

---

#### `annonce_delete(p_id uuid)` -> boolean
**Exigence 22**: Supprimer une annonce

**Restriction**: Seul le vendeur ou un admin peut supprimer

---

#### `annonce_list(p_filters jsonb)` -> SETOF annonce
**Exigences 3-4**: Rechercher et trier les annonces

**Paramètres**:
```json
{
  "search": "string (titre/description)",
  "marque": "string (nom marque)",
  "couleur": "string (nom couleur)",
  "materiau": "string (nom materiau)",
  "etat": "string",
  "taille_systeme": "EU|UK|US",
  "taille": "string",
  "prix_min": "decimal",
  "prix_max": "decimal",
  "disponible": "boolean",
  "order_by": "prix|date_publication|titre (défaut: date_publication)",
  "order_dir": "ASC|DESC (défaut: DESC)"
}
```

**Retour**: Annonces filtrées et triées

---

#### `annonce_list_by_vendeur(p_id_vendeur uuid)` -> SETOF annonce
**Exigence 23**: Lister les annonces d'un vendeur

---

### 4. IMAGE (3 procédures)

#### `image_create(p_id_annonce uuid, p_data jsonb)` -> image
**Exigence 23**: Ajouter une image à une annonce

**Paramètres**:
```json
{
  "url": "string",
  "est_principale": "boolean (défaut: false)"
}
```

**Contrainte**: Maximum 5 images par annonce (vérification dans PHP)

---

#### `image_delete(p_id integer)` -> boolean
Supprimer une image

---

#### `image_list_by_annonce(p_id_annonce uuid)` -> SETOF image
Lister les images d'une annonce (principale en premier)

---

### 5. COMMANDE (5 procédures)

#### `commande_create(p_id_utilisateur uuid, p_articles jsonb, p_montant decimal)` -> commande
**Exigences 13, 15**: Créer une commande directement avec articles

**Paramètres**:
- `p_id_utilisateur`: UUID de l'acheteur
- `p_articles`: Array JSON `[{"id_annonce": "uuid", "quantite": 1}, ...]`
- `p_montant`: Montant total calculé côté client

**Processus**:
1. Crée un paiement avec le montant fourni
2. Crée la commande liée au paiement
3. Ajoute les articles dans DETAILLER_COMMANDE
4. Marque les annonces comme vendues (trigger)

**Retour**: Commande créée

---

#### `commande_read(p_id uuid)` -> commande
**Exigence 16**: Consulter une commande

---

#### `commande_update_statut(p_id uuid, p_statut text)` -> commande
**Exigence 26**: Mettre à jour le statut d'une commande

**Statuts**: en_attente, validee, expediee, livree, annulee

**Trigger activé**: SA003 (pas de modification si livrée)

---

#### `commande_annuler(p_id uuid)` -> boolean
**Exigence 13**: Annuler une commande

**Note**: Impossible si statut = 'livree' (trigger SA003)

---

#### `commande_list_by_user(p_id_utilisateur uuid)` -> SETOF commande
**Exigence 16**: Lister les commandes d'un utilisateur

---

### 8. PAIEMENT (4 procédures)

#### `paiement_create(p_data jsonb)` -> paiement
**Exigence 14-15**: Créer un paiement

**Paramètres**:
```json
{
  "type": "carte_bancaire|paypal|virement",
  "montant_paye": "decimal"
}
```

**Statut**: 'valide' par défaut (validation effectuée par service tiers externe)

---

#### `paiement_read(p_id integer)` -> paiement
Consulter un paiement

---

#### `paiement_update_statut(p_id integer, p_statut text)` -> paiement
**Exigence 25**: Mettre à jour le statut du paiement

**Statuts**: en_attente, valide, echoue, rembourse

---

#### `paiement_list_by_user(p_id_utilisateur uuid)` -> SETOF paiement
Lister les paiements d'un utilisateur

---

### 9. REVIEW (5 procédures)

#### `review_create(p_id_auteur uuid, p_id_vendeur uuid, p_data jsonb)` -> review
**Exigence 18**: Écrire un avis sur un vendeur

**Paramètres**:
```json
{
  "note": "integer (1-5)",
  "commentaire": "string (optionnel)"
}
```

**Règles métier**:
1. L'acheteur doit avoir acheté au moins un produit (commande non annulée) du vendeur
2. Un seul avis autorisé par couple acheteur-vendeur

**Erreurs possibles**:
- SA023: L'acheteur a déjà laissé un avis pour ce vendeur
- SA024: L'acheteur n'a jamais acheté de produit chez ce vendeur

---

#### `review_read(p_id integer)` -> review
Consulter un avis

---

#### `review_update(p_id integer, p_data jsonb)` -> review
**Exigence 19**: Modifier un avis

**Paramètres**: note et/ou commentaire

---

#### `review_delete(p_id integer)` -> boolean
**Exigence 19**: Supprimer un avis

---

#### `review_list_by_vendeur(p_id_vendeur uuid, p_limit int, p_offset int, p_exclude_review_id int)` -> SETOF review
**Exigence 18**: Lister les avis d'un vendeur avec pagination

**Paramètres**:
- `p_id_vendeur`: UUID du vendeur
- `p_limit`: Nombre d'avis à retourner (NULL = tous, défaut)
- `p_offset`: Décalage pour la pagination (défaut: 0)
- `p_exclude_review_id`: ID d'un avis à exclure (NULL = aucune exclusion, défaut)

**Utilisation**: Permet la pagination côté SQL pour performances optimales. Utile pour exclure l'avis de l'utilisateur connecté affiché séparément en haut de page.

---


### 10. SIGNALEMENT (3 procédures)

#### `signalement_create(p_id_auteur uuid, p_type text, p_id_cible uuid, p_data jsonb)` -> signalement
**Exigence 29a**: Créer un signalement polymorphe

**Paramètres**:
```json
{
  "motif": "string",
  "description": "string"
}
```
Arguments :
- `p_id_auteur` : UUID de l'auteur du signalement
- `p_type` : 'user', 'annonce', 'review'
- `p_id_cible` : UUID de la cible (utilisateur, annonce ou review)

**Statut**: 'en_attente' par défaut

**Trigger activé**: SA012 (auto-signalement interdit pour type user), vérification d'existence de la cible selon le type

----

#### `signalement_traiter(p_id integer, p_id_admin uuid, p_decision text, p_raison text)` -> signalement
**Exigence 32**: Traiter un signalement (admin)

**Paramètres**:
- `p_decision`: 'resolu' ou 'rejete'
- `p_raison`: Justification de la décision

**Log**: Crée une entrée dans ADMIN_LOG

----

#### `signalement_list(p_filters jsonb)` -> SETOF signalement
**Exigence 32**: Lister les signalements

**Paramètres**:
```json
{
  "statut": "en_attente|traite|rejete (optionnel)",
  "type": "user|annonce|review (optionnel)",
  "id_cible": "uuid (optionnel)"
}
```

----

### 11. CONSENTEMENT_UTILISATEUR (3 procédures)

#### `consentement_create(p_id_utilisateur uuid, p_data jsonb)` -> consentement_utilisateur
**GDPR Article 7**: Enregistrer un consentement

**Paramètres**:
```json
{
  "type_consentement": "cookies|conditions_utilisation|traitement_donnees|marketing",
  "statut": "boolean (true = consentement donné)"
}
```

---

#### `consentement_update(p_id integer, p_statut boolean)` -> consentement_utilisateur
**GDPR**: Modifier un consentement

**Note**: Si statut = FALSE, met à jour date_retrait

---

#### `consentement_list_by_user(p_id_utilisateur uuid)` -> SETOF consentement_utilisateur
**Exigence 11**: Lister les consentements d'un utilisateur

---

### 12. ADMIN_LOG (2 procédures)

#### `admin_log_create(p_id_admin uuid, p_data jsonb)` -> admin_log
**GDPR Article 30**: Enregistrer une action admin

**Paramètres**:
```json
{
  "action_type": "bannir_utilisateur|suspendre_utilisateur|supprimer_annonce|...",
  "id_cible": "string (UUID ou ID)",
  "raison": "string",
  "ip_address": "string"
}
```

**Trigger activé**: SA016 (seuls les admins peuvent créer des logs)

---

#### `admin_log_list(p_filters jsonb)` -> SETOF admin_log
**Exigence 30**: Consulter les logs admin

**Paramètres**:
```json
{
  "action_type": "string (optionnel)",
  "id_utilisateur": "uuid (optionnel)",
  "date_debut": "timestamp (optionnel)",
  "date_fin": "timestamp (optionnel)"
}
```

---

### 13. Tables de référence (3 procédures)

#### `marque_list()` -> SETOF marque
**Exigence 4**: Lister toutes les marques

---

#### `couleur_list()` -> SETOF couleur
**Exigence 4**: Lister toutes les couleurs

---

#### `materiau_list()` -> SETOF materiau
**Exigence 4**: Lister tous les matériaux

---

### 14. Statistiques (1 procédure)

#### `vendeur_get_statistiques(p_id_vendeur uuid)` -> TABLE(...)
**Exigence 24**: Statistiques de ventes pour un vendeur

**Retour**: Table avec:
- total_ventes: Nombre total de ventes
- montant_total: Chiffre d'affaires total
- note_moyenne: Note moyenne des reviews
- nb_reviews: Nombre d'avis reçus

---

### 15. AI Embeddings - Recherche sémantique (3 procédures)

#### `annonce_update_embeddings(p_id_annonce uuid, p_embeddings vector(384))` -> boolean
Met à jour les embeddings d'une annonce (appelé depuis PHP après génération Python)

**Paramètres**:
- `p_id_annonce`: UUID de l'annonce
- `p_embeddings`: Vecteur 384 dimensions généré par le modèle Python

**Retour**: TRUE si mise à jour réussie

**Usage**: Appelé après qu'une annonce soit créée/modifiée pour générer sa représentation vectorielle

---

#### `annonce_search_by_embedding(p_search_vector vector(384), p_limit integer)` -> TABLE(...)
Recherche sémantique par similarité cosinus

**Paramètres**:
- `p_search_vector`: Vecteur de recherche (généré à partir du texte de recherche utilisateur)
- `p_limit`: Nombre maximum de résultats (défaut: 10)

**Retour**: Table avec:
- id_annonce, titre, description, prix
- similarity_score: Score de similarité (0-1, plus élevé = plus similaire)

**Filtres automatiques**:
- Seulement annonces avec embeddings non-NULL
- Seulement annonces disponibles (`disponible = TRUE`)

**Tri**: Par similarité décroissante (les plus similaires en premier)

---

#### `annonce_find_similar(p_id_annonce uuid, p_limit integer)` -> TABLE(...)
Trouve des produits similaires à une annonce donnée

**Paramètres**:
- `p_id_annonce`: UUID de l'annonce source
- `p_limit`: Nombre de suggestions (défaut: 5)

**Retour**: Table avec:
- id_annonce, titre, prix
- similarity_score: Score de similarité avec l'annonce source

**Usage**: Recommandations de produits similaires ("Vous aimerez aussi")

**Filtres automatiques**:
- Exclut l'annonce source
- Seulement annonces avec embeddings non-NULL
- Seulement annonces disponibles (`disponible = TRUE`)

**Note technique**: Utilise l'opérateur de distance cosinus `<=>` de pgvector. Score calculé comme `1 - distance`.

---

## Exécution des tests via les scripts

### Via docker.sh

```bash
# Tester les triggers
./docker.sh test-triggers

# Tester les procédures stockées
./docker.sh test-procedures

# Tester les procédures AI embeddings
./docker.sh test-embeddings

# Exécuter un script SQL personnalisé
./docker.sh sql database/01-schema.sql
./docker.sh sql database/scripts/test_triggers.sql
```

---

## Optimisations de performance: Champs cachés automatiquement mis à jour

Pour éviter des recalculs coûteux à chaque requête, certaines statistiques agrégées sont stockées directement dans la table UTILISATEUR et maintenues automatiquement par des triggers:

### Statistiques d'avis (Reviews)
**Champs**: `note_moyenne`, `nombre_avis`

**Triggers**:
- `trg_review_after_insert` - Recalcule après création d'avis
- `trg_review_after_update` - Recalcule après modification d'avis
- `trg_review_after_delete` - Recalcule après suppression d'avis

**Fonction helper**: `update_user_review_stats(p_id_vendeur UUID)`

**Avantage**: Évite de scanner tous les avis à chaque affichage de profil. Performance O(1) au lieu de O(n).

### Statistiques de vente
**Champs**: `montant_total_ventes`, `montant_mois_actuel`, `montant_annee_actuelle`

**Trigger**:
- `trg_sales_after_paiement_update` - Mise à jour UNIQUEMENT quand le statut de paiement change (ex: 'en_attente' → 'valide', ou 'valide' → 'rembourse')

**Fonction helper**: `update_user_sales_stats(p_id_vendeur UUID)` - Recalcule les trois montants en une seule requête

**Logique**:
- Les stats sont mises à jour SEULEMENT quand le paiement est confirmé/validé, pas quand les produits sont ajoutés au panier
- Seuls les paiements avec `statut='valide'` comptent dans les statistiques
- Le trigger se déclenche sur UPDATE, pas sur INSERT (correspond au flux normal: INSERT avec 'en_attente', puis UPDATE vers 'valide')

**Initialisation**: Le fichier `04-data.sql` appelle manuellement `update_user_sales_stats()` pour chaque vendeur à la fin, car l'insertion initiale des données ne déclenche pas le trigger UPDATE.

**Avantage**: Les trois statistiques (mois, année, total) sont toutes cachées et maintenues automatiquement. Aucun calcul à la demande, performance O(1) pour afficher la page de ventes.

**Note**: Cette approche est utilisée par les grandes plateformes (Amazon, eBay, etc.) pour maintenir des performances optimales avec de gros volumes de données.

---

*Ce README et certaines des requêtes SQL ont été générés avec l'aide de Claude Code (Claude Opus 4.5)*
