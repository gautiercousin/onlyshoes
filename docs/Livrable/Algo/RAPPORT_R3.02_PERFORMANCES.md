# Rapport R3.02 - Algorithmes Corrects et Efficaces
## SAE3.01 - OnlyShoes

**Date:** 15/01/2025
**Équipe:** Cousin Gautier, Guirado Jessy, Naouach Hannibal, Terrien Swan

---

## Table des matières
1. [Correction de l'application](#1-correction-de-lapplication)
2. [Efficacité en temps et mémoire](#2-efficacité-en-temps-et-mémoire)
3. [Résultats des tests](#3-résultats-des-tests)
4. [Conclusion](#4-conclusion)

---

## 1. Correction de l'application

### 1.1 Fonctionnalités et préconditions

| Fonctionnalité | Préconditions | Résultat attendu |
|----------------|---------------|------------------|
| Inscription (Exigence 6) | Email valide, mdp haché bcrypt | Compte créé, session initiée |
| Connexion (Exigence 7) | Email existant, mdp correct, compte non banni | Session utilisateur |
| Consulter annonce (Exigence 2) | ID annonce valide | Détails complets + vendeur |
| Recherche/filtres (Exigences 3-4) | Filtres optionnels (prix, marque, état) | Liste triée d'annonces |
| Publier annonce (Exigences 20-23) | Utilisateur connecté, données complètes | Annonce créée avec image |
| Passer commande (Exigence 13) | Panier non vide, annonces disponibles | Commande + paiement créés |
| Payer (Exigences 14, 25) | Commande existante, montant correct | Paiement validé |
| Laisser avis (Exigence 19) | Achat livré chez ce vendeur, 1 avis max | Review créée |
| Signaler (Exigence 29a) | Cible existante (user/annonce/review) | Signalement en attente |
| Supprimer compte (Exigence 10) | Utilisateur connecté | Suppression ou anonymisation RGPD |

### 1.2 Assertions et validations dans le code

#### Triggers de validation (12 triggers actifs)

```
Source: database/startup/02-triggers.sql
```

| Trigger | Table | Validation |
|---------|-------|------------|
| `trg_verif_password_hash` | UTILISATEUR | Vérifie format bcrypt (`^\$2[aby]\$\d{2}\$.{53}$`) |
| `trg_verif_email_format` | UTILISATEUR | Regex email valide |
| `trg_verif_paiement_valide` | COMMANDE | Paiement doit être 'valide' avant commande |
| `trg_verif_vendeur_acheteur` | DETAILLER_COMMANDE | Empêche auto-achat (SA006) |
| `trg_verif_review_autorisee` | REVIEW | Vérifie achat préalable livré (SA010) |
| `trg_verif_signalement_integrite` | SIGNALEMENT | Vérifie existence cible selon type |
| `trg_verif_modification_commande` | COMMANDE | Bloque modification si livrée (SA003) |
| `trg_verif_admin_action` | ADMIN_LOG | Vérifie type_compte = 'admin' (SA016) |
| `trg_verif_modification_status` | UTILISATEUR | Empêche ban d'admin (SA020) |
| `trg_marquer_annonce_vendue` | DETAILLER_COMMANDE | Auto-marque annonce indisponible |
| `trg_review_after_*` | REVIEW | Recalcule stats vendeur automatiquement |
| `trg_sales_after_paiement_update` | PAIEMENT | Recalcule montants ventes |

#### Codes d'erreur personnalisés (SQLSTATE)

| Code | Message | Contexte |
|------|---------|----------|
| SA003 | Impossible de modifier une commande livrée | UPDATE COMMANDE |
| SA006 | Vous ne pouvez pas acheter votre propre annonce | INSERT DETAILLER_COMMANDE |
| SA010 | Achat requis pour laisser un avis | INSERT REVIEW |
| SA012 | Auto-signalement interdit | INSERT SIGNALEMENT |
| SA013 | Paiement doit être validé | INSERT COMMANDE |
| SA015 | Mot de passe non haché détecté | INSERT/UPDATE UTILISATEUR |
| SA016 | Droits admin requis | INSERT ADMIN_LOG |
| SA017 | Format email invalide | INSERT/UPDATE UTILISATEUR |
| SA020 | Impossible de bannir un admin | UPDATE UTILISATEUR |
| SA021-24 | Cible inexistante selon type | INSERT SIGNALEMENT |

### 1.3 Procédures stockées (40+ fonctions)

```
Source: database/startup/03-procedures.sql
```

Toutes les opérations CRUD passent par des procédures stockées avec validation:
- **utilisateur_*** : CRUD + login + historique achats
- **annonce_*** : CRUD + recherche avec filtres + recherche sémantique
- **commande_*** : Création transactionnelle avec articles
- **paiement_*** : Validation + confirmation
- **review_*** : Avec vérification achat préalable
- **signalement_*** : Polymorphe (user/annonce/review)

---

## 2. Efficacité en temps et mémoire

### 2.1 Traitement optimal des données

#### Normalisation 3NF

Le schéma respecte la 3ème forme normale:
- **Pas de redondance** : marques, couleurs, matériaux dans tables séparées
- **Dépendances fonctionnelles** : chaque attribut dépend uniquement de la clé primaire
- **Pas de transitivité** : adresses liées à utilisateur, pas dupliquées dans commandes

```sql
-- Exemple: annonce référence les tables de référence par FK
annonce (
    id_marque -> marque.id_marque,
    id_couleur -> couleur.id_couleur,
    id_materiau -> materiau.id_materiau
)
```

#### SGBD choisi: PostgreSQL 15 + pgvector

| Avantage | Description |
|----------|-------------|
| **UUID natif** | Clés primaires UUID sans extension |
| **JSONB** | Filtres dynamiques dans procédures |
| **pgvector** | Recherche sémantique vectorielle (384 dimensions) |
| **Triggers BEFORE/AFTER** | Validation au niveau SGBD |
| **Index B-tree** | Optimisation automatique des recherches |

#### Procédures ad hoc

Les requêtes complexes sont encapsulées:
```sql
-- Recherche avec filtres dynamiques (évite N requêtes)
annonce_list(p_filters jsonb) -- Un seul appel pour tous les filtres

-- Statistiques vendeur pré-calculées
vendeur_get_statistiques(p_id_vendeur uuid) -- Agrégation optimisée
```

### 2.2 Montée en charge - Tests de performance

#### Résultats des tests de charge

```
Source: tests/load_test/concurrent_requests.txt
```

| Test | Requêtes | Min | Max | Moyenne |
|------|----------|-----|-----|---------|
| Homepage (/) | 50 concurrent | 0.049s | 0.290s | **0.167s** |
| Annonces (/annonces) | 100 concurrent | 0.004s | 0.085s | **0.026s** |
| Recherche sémantique | 50 concurrent | 0.365s | 2.146s | **1.279s** |

**Analyse:**
- Pages statiques: < 200ms sous charge (acceptable)
- API annonces: < 100ms même avec 100 requêtes simultanées (excellent)
- Recherche sémantique: ~1.3s moyenne (acceptable, implique Flask + pgvector)

### 2.3 Points consommateurs de temps

#### Temps de chargement des pages

```
Source: tests/curl_timing/page_load_times.txt
```

| Page | TTFB moyen | Analyse |
|------|------------|---------|
| `/` (accueil) | 55ms | Rapide, page PHP simple |
| `/annonces` | 7ms | Très rapide, cache probable |
| `/connexion` | 8ms | Formulaire statique |
| `/inscription` | 8ms | Formulaire statique |
| `/recherche?q=nike` | **89ms** | Plus lent (Flask API + pgvector) |
| `/api/annonces` | 4ms | API JSON optimisée |

#### Images - Analyse des poids

```
Source: tests/images/image_sizes.txt
```

| Fichier | Taille | Statut |
|---------|--------|--------|
| product_1768237093.jpeg | 17 KB | OK |
| nike-air-max-1.png | 20 KB | OK |
| rounded-logo.webp | 29 KB | OK (WebP optimisé) |
| notfound.webp | 51 KB | OK (converti depuis PNG 1.4MB) |
| product_1768332688.jpg | 223 KB | Acceptable |
| product_1768234238.png | 295 KB | Acceptable |

**Total:** 644 KB pour 6 images - Optimisé

**Recommandations futures:**
1. Compresser les images produits > 200KB lors de l'upload
2. Implémenter lazy loading pour les listes d'annonces

### 2.4 Scénarios de montée en charge

| Scénario | Description | Impact estimé | Mitigation |
|----------|-------------|---------------|------------|
| **Black Friday** | x10 trafic sur 24-48h | +1000 req/min | Cache Redis, CDN images |
| **Drop limité** | Sortie sneakers limitées | Pics de 500 req/s sur /recherche | Rate limiting, queue |
| **Viral TikTok** | Afflux soudain nouveaux users | +5000 inscriptions/jour | Scaling horizontal web |

### 2.5 Plan de dimensionnement - Stockage

#### Taille actuelle de la base

```
Source: tests/database/db_sizes.txt
```

| Élément | Taille |
|---------|--------|
| Base totale | **8.8 MB** |
| Table annonce | 72 KB |
| Table signalement | 64 KB |
| Table utilisateur | 48 KB |
| Autres tables | < 40 KB chacune |

#### Projection de croissance

| Période | Utilisateurs | Annonces | Taille DB estimée |
|---------|--------------|----------|-------------------|
| Actuel | 10 | 69 | 8.8 MB |
| 6 mois | 500 | 2,000 | ~100 MB |
| 1 an | 2,000 | 10,000 | ~500 MB |
| 2 ans | 10,000 | 50,000 | ~2.5 GB |

**Note:** Les embeddings vectoriels (384 float32 = 1.5KB/annonce) représenteront ~75MB pour 50,000 annonces.

### 2.6 Organisation des données et déploiement

#### Architecture Docker

```
┌─────────────────────────────────────────────────────────┐
│                     Docker Network                      │
├─────────────┬─────────────┬──────────────┬──────────────┤
│   sae_web   │sae_postgres │sae_embeddings│ sae_adminer  │
│  (PHP/CI4)  │(PostgreSQL) │   (Flask)    │  (Adminer)   │
│   :8080     │   :5432     │    :5000     │    :8082     │
├─────────────┴─────────────┴──────────────┴──────────────┤
│                    Volume: postgres-data                │
└─────────────────────────────────────────────────────────┘
```

#### Flux de données

1. **Requête utilisateur** -> sae_web (CodeIgniter 4)
2. **Opérations DB** -> sae_postgres (procédures stockées)
3. **Recherche sémantique** -> sae_embeddings (Flask) -> sae_postgres (pgvector)

### 2.7 Dimensionnement serveur

#### Usage ressources mesuré

```
Source: tests/resources/docker_stats.txt
```

| Container | CPU | RAM | % RAM |
|-----------|-----|-----|-------|
| sae_web | 0% idle | 67 MB | 0.88% |
| sae_postgres | 0% idle | 43 MB | 0.56% |
| sae_embeddings | 0.03% | **872 MB** | **11.43%** |
| sae_adminer | 0% idle | 11 MB | 0.15% |

**Total:** ~1 GB RAM utilisé sur 7.4 GB disponible

#### Recommandations de dimensionnement

| Environnement | CPU | RAM | Stockage |
|---------------|-----|-----|----------|
| **Dev/Test** | 2 cores | 4 GB | 20 GB SSD |
| **Production (petit)** | 4 cores | 8 GB | 50 GB SSD |
| **Production (moyen)** | 8 cores | 16 GB | 100 GB SSD |

### 2.8 Impact du choix d'hébergement

#### Limitation des tests

**Nous n'avons pas accès à des équipements Cloud gratuits** (AWS, GCP, Azure) permettant de réaliser des tests de latence en conditions réelles de production. Les crédits étudiants ne sont pas disponibles pour notre formation.

#### Analyse théorique

| Facteur | Impact sur les temps de réponse |
|---------|--------------------------------|
| **Latence réseau** | +5-150ms selon distance serveur-utilisateur |
| **Type de stockage** | SSD vs HDD : jusqu'à 10x différence sur les I/O DB |
| **Bande passante** | Affecte principalement le chargement des images |
| **Colocation DB/App** | Séparer PostgreSQL du serveur web ajoute ~2-10ms/requête |

#### Recommandations basées sur l'architecture

Notre application Docker est **portable** et peut être déployée sur n'importe quel hébergement supportant Docker :

1. **VPS France (OVH, Scaleway)** : Latence minimale pour utilisateurs français
2. **Hébergement mutualisé** : Non recommandé (pas de Docker, performances variables)
3. **Cloud EU** : Acceptable si budget disponible, CDN recommandé pour les images

**Conclusion** : Les tests locaux (latence 0ms) représentent le meilleur cas. En production, ajouter **+10-50ms** pour un hébergement VPS France.

---

## 3. Résultats des tests

### 3.1 Performance des requêtes SQL

```
Source: tests/database/explain_analyze.txt
```

| Requête | Temps planification | Temps exécution | Méthode |
|---------|---------------------|-----------------|---------|
| User lookup by email | 1.45ms | **0.055ms** | Seq Scan (10 rows) |
| Signalements + JOINs | 2.05ms | **0.338ms** | Hash Join + QuickSort |

**Note:** Avec le volume actuel (10 users, 52 signalements), les Seq Scan sont optimaux. Les index B-tree prendront le relais automatiquement au-delà de ~1000 lignes.

### 3.2 Index utilisés

```sql
-- Index les plus sollicités (tests/database/db_sizes.txt)
annonce_pkey             : 832 utilisations
detailler_commande_pkey  : 674 utilisations
commande_id_paiement_key : 633 utilisations
utilisateur_pkey         : 335 utilisations
```

### 3.3 Fichiers de test disponibles

```
docs/Livrable/Algo/tests/
├── curl_timing/
│   └── page_load_times.txt      # Temps de chargement pages
├── load_test/
│   └── concurrent_requests.txt  # Tests 50-100 requêtes parallèles
├── database/
│   ├── db_sizes.txt             # Tailles tables et index
│   ├── explain_analyze.txt      # Plans d'exécution SQL
│   └── stored_procedures.txt    # Liste procedures/triggers
├── resources/
│   └── docker_stats.txt         # Usage CPU/RAM containers
└── images/
    └── image_sizes.txt          # Analyse poids images
```

---

## 4. Conclusion

### Points forts

1. **Correction garantie** par 12 triggers de validation au niveau SGBD
2. **Performance excellente** : < 100ms pour 90% des pages
3. **Architecture scalable** : Docker, procédures stockées, pgvector
4. **RGPD compliant** : anonymisation au lieu de suppression si commandes livrées
5. **Images optimisées** : Total 644 KB (notfound converti de 1.4MB PNG vers 51KB WebP)

### Points d'amélioration futurs

1. **Recherche sémantique** : 1.3s moyenne -> Ajouter cache Redis
2. **Index email** : Ajouter index sur `utilisateur.email` pour login rapide
3. **Compression upload** : Compresser automatiquement les images > 200KB

### Outils utilisés

- **curl** : Mesure temps de réponse HTTP
- **docker stats** : Monitoring ressources containers
- **psql EXPLAIN ANALYZE** : Analyse plans d'exécution SQL
- **find/du** : Analyse taille fichiers

---

### Assistance technique

Certaines des tests et conclusions ont été réalisés avec l'aide de **Claude Code** (Anthropic, modèle Claude Opus 4.5). L'ensemble de ce PDF a également été restructuré afin d'être plus naturel, transparent et intuitif par cette même IA.
