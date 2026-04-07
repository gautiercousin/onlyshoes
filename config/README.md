# Configuration Apache

## Système de Placeholders

Les fichiers de configuration Apache utilisent des **placeholders** automatiquement remplacés au démarrage par `docker/entrypoint.sh`.

### Placeholders Disponibles

| Placeholder | Description | CodeIgniter=true | CodeIgniter=false |
|-------------|-------------|------------------|-------------------|
| `{{DOCUMENT_ROOT}}` | Racine du site | `/var/www/html/public` | `/var/www/html` |

### Exemple d'utilisation

```apache
<VirtualHost *:80>
    DocumentRoot {{DOCUMENT_ROOT}}
    
    <Directory {{DOCUMENT_ROOT}}>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## Modification de la Configuration

Pour personnaliser Apache :

1. Éditez la configuration (utilisez `{{DOCUMENT_ROOT}}` pour les chemins dynamiques)
2. Reconstruisez : `./docker.sh reconstruire`

---

*Ce README et certaines des configurations Docker ont été générés avec l'aide de Claude Code (Claude Opus 4.5)*