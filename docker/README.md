# Docker - Projet SAE

## Stack Technique
- **PHP 8.3** + Apache (extensions: PDO, pdo_pgsql, pgsql, zip, intl, mysqli)
- **PostgreSQL 17** + **pgvector** (extension pour vecteurs/embeddings)
- **Adminer** (interface web pour gérer la base de données)
- **Composer** (inclus dans le conteneur web)
- **CodeIgniter 4** (framework PHP)

## Scripts disponibles

| Script | Plateforme | Description |
|--------|------------|-------------|
| `docker.sh` | Linux/macOS | Script principal |
| `podman.sh` | Linux | Alternative Podman |
| `run_docker_mac.sh` | macOS | Script simplifié Mac |

## Commandes Principales

```bash
./docker.sh              # Afficher le menu d'aide
./docker.sh demarrer     # Démarrer les conteneurs
./docker.sh arreter      # Arrêter les conteneurs
./docker.sh logs         # Voir les logs (Ctrl+C pour quitter)
./docker.sh status       # État des conteneurs
./docker.sh reconstruire # Reconstruire après modification
```

## Commandes Avancées

```bash
./docker.sh redemarrer   # Redémarrer sans reconstruire
./docker.sh shell        # Terminal dans le conteneur web
./docker.sh db-reset     # Réinitialiser la base de données
./docker.sh nettoyer     # Nettoyer (supprime volumes)
./docker.sh permissions  # Corriger les permissions
```

## Services et Ports

| Service | URL | Identifiants |
|---------|-----|-------------|
| **Site web** | http://localhost:8080 | - |
| **Adminer** | http://localhost:8082 | Serveur: `db`, User: `sae_user`, Pass: `sae_password` |
| **PostgreSQL** | localhost:5432 | User: `sae_user`, Pass: `sae_password` |

## Base de Données

### Extension pgvector
L'extension **pgvector** est installée automatiquement. Elle permet de stocker et rechercher des vecteurs (embeddings) pour la recherche sémantique.

### Scripts SQL
Les scripts dans `database/startup/` sont exécutés par ordre alphabétique au premier démarrage.

### Accès PostgreSQL

```bash
./docker.sh shell
psql -U sae_user -d sae_database
```

## Développement

### Appliquer les modifications
Les fichiers `src/` sont copiés dans l'image Docker au build. Pour appliquer vos modifications :
```bash
./docker.sh reconstruire
```

### Structure CodeIgniter
- Contrôleurs : `src/codeigniter/app/Controllers/`
- Vues : `src/codeigniter/app/Views/`
- Modèles : `src/codeigniter/app/Models/`
- Routes : `src/codeigniter/app/Config/Routes.php`

## Problèmes de Permissions

```bash
./docker.sh permissions
```

Ce problème arrive si Docker a créé des dossiers avec les permissions root.

---

*README mis à jour avec l'aide de Claude Code (Claude Opus 4.5)*
