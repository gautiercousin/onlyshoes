#!/bin/bash
set -e

echo "=== Starting CodeIgniter Application ==="
DOC_ROOT="/src/codeigniter/public"

# Fix writable directory permissions (keep it sane, not 777)
echo "=== Fixing writable directory permissions ==="
if [ -d "/src/codeigniter/writable" ]; then
    chown -R www-data:www-data /src/codeigniter/writable 2>/dev/null || true
    find /src/codeigniter/writable -type d -exec chmod 775 {} \; 2>/dev/null || true
    find /src/codeigniter/writable -type f -exec chmod 664 {} \; 2>/dev/null || true
    echo "Writable permissions set to 775/664 (www-data)"
fi

# Process Apache config templates
echo "=== Processing Apache configuration ==="
for template_source in /etc/apache2/sites-available-templates/*.conf; do
    if [ -f "$template_source" ]; then
        filename=$(basename "$template_source")
        target="/etc/apache2/sites-available/$filename"
        cp "$template_source" "$target"
        sed -i "s|{{DOCUMENT_ROOT}}|$DOC_ROOT|g" "$target"
        echo "  -> $filename configured with DocumentRoot=$DOC_ROOT"
    fi
done

# Silence ServerName warning (optional, harmless but nice)
echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf
a2enconf servername >/dev/null 2>&1 || true

echo "=== Starting Apache ==="
exec apache2-foreground
