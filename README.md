# SAE - Projet Web (OnlyShoes)

**Équipe 1** : COUSIN-Gautier, GUIRADO-Jessy, NAOUACH-Hannibal, TERRIEN-Swan
# Pour voir la preview du site,merci de regarder previewSite.pdf disponible à la racine du projet
## Démarrage Rapide




### Prérequis
- Docker & Docker Compose (ou Podman)

### Lancement

**Linux/Windows (WSL):**
```bash
./docker.sh demarrer
```

**macOS:**
```bash
./run_docker_mac.sh
# ou
./docker.sh demarrer
```

**Alternative Podman:**
```bash
./podman.sh demarrer
```

### Commandes disponibles

```bash
./docker.sh              # Affiche toutes les commandes
./docker.sh demarrer     # Démarrer les conteneurs
./docker.sh arreter      # Arrêter les conteneurs
./docker.sh reconstruire # Reconstruire et redémarrer
./docker.sh logs         # Voir les logs
./docker.sh shell        # Ouvrir un shell dans le conteneur web
./docker.sh permissions  # Corriger les permissions fichiers
./docker.sh db-reset     # Réinitialiser la base de données
```

### Accès aux services
| Service | URL | Notes |
|---------|-----|-------|
| Site web | http://localhost:8080 | Application principale |
| Admin | http://localhost:8080/admin | Interface administration |
| Adminer | http://localhost:8082 | Gestion BDD (user: `sae_user`, pwd: `sae_password`) |

## Structure du Projet

```
├── docker.sh              # Script Docker (Linux/Mac)
├── podman.sh              # Script Podman (alternative)
├── run_docker_mac.sh      # Script simplifié macOS
├── docker/                # Configuration Docker
├── database/              # Scripts SQL (exécutés au démarrage)
├── src/codeigniter/       # Application CodeIgniter 4
│   ├── app/Controllers/   # Contrôleurs
│   ├── app/Views/         # Vues
│   ├── app/Models/        # Modèles
│   └── app/Config/        # Configuration
└── docs/                  # Documentation et livrables
```

## Base de Données

### Scripts SQL
Les fichiers dans `database/startup/` sont exécutés automatiquement au premier démarrage :
- `00-activate.sql` : Active l'extension pgvector
- `01-init.sql` : Structure des tables
- `02-triggers.sql` : Triggers de validation
- `03-procedures.sql` : Procédures stockées
- `04-data.sql` : Données initiales

### Gestion
```bash
./docker.sh db-reset                    # Réinitialiser (supprime les données)
./docker.sh sql database/scripts/xxx.sql # Exécuter un script SQL
```

## Recherche Sémantique IA

Le projet intègre une recherche sémantique intelligente via pgvector + sentence-transformers.

**Recherche traditionnelle** : Mots-clés exacts uniquement
**Recherche sémantique** : Comprend le sens et les synonymes

| Modèle | sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2 |
|--------|-------------------------------------------------------------|
| Taille | ~118 MB |
| Langues | Multilingue (FR, EN, etc.) |
| Dimensions | 384 |

```
Utilisateur → Recherche "sneakers running"
    ↓
Python (Flask) → Génère vecteur 384D
    ↓
PostgreSQL (pgvector) → Compare aux annonces
    ↓
Résultats triés par similarité
```

## Problèmes Courants

### Permissions de fichiers
```bash
./docker.sh permissions
```

### Les changements ne s'appliquent pas
```bash
./docker.sh reconstruire
```

### Voir les logs d'erreur
```bash
./docker.sh logs
```

### macOS : Docker ne démarre pas
Le script `run_docker_mac.sh` ou `docker.sh` détecte automatiquement macOS et lance Docker Desktop si nécessaire.

---

*README mis à jour avec l'aide de Claude Code (Claude Opus 4.5)*
