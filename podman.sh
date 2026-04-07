#!/bin/bash
set -e

COMPOSE_CMD="podman-compose"
COMPOSE_FILE="docker/docker-compose.yml"
ENV_FILE=".env"

export USER_ID="$(id -u)"
export GROUP_ID="$(id -g)"

check_sudo() {
    SUDO=""
    echo "Checking Podman..."
    if ! podman ps >/dev/null 2>&1; then
        if [ "$(uname -s)" = "Darwin" ]; then
            echo "Podman not running. Starting podman machine..."
            if ! podman machine start >/dev/null 2>&1; then
                echo "No podman machine found. Initializing..."
                podman machine init
                podman machine start
            fi
            echo "Podman is running"
            return 0
        fi
        echo "Podman not running. Start Podman, then retry."
        exit 1
    fi
}

composer_install() {
    # Check if vendor exists inside the container, not on host
    if $SUDO podman exec sae_web test -f "/src/codeigniter/vendor/autoload.php" 2>/dev/null; then
        echo "Composer: already installed"
        return 0
    fi

    echo "Composer: installing dependencies..."
    $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE up -d web
    $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE exec web bash -lc "
        cd /src/codeigniter && \
        composer install --no-interaction --prefer-dist
    "
    echo "Composer: done"
}

menu() {
    echo "Docker - Projet SAE (podman-compose)"
    echo ""
    echo "Commands:"
    echo "  demarrer        Start containers"
    echo "  arreter         Stop containers"
    echo "  redemarrer      Restart containers"
    echo "  reconstruire    Rebuild and restart"
    echo "  logs            Follow logs"
    echo "  status          Show container status"
    echo "  nettoyer        Remove containers and volumes"
    echo "  db-reset        Reset database (removes data)"
    echo "  shell           Open shell in web container"
    echo "  permissions     Fix file permissions"
    echo "  push            Sync local files to containers"
    echo ""
    echo "Database:"
    echo "  test-triggers"
    echo "  test-procedures"
    echo "  test-embeddings"
    echo "  populate-embeddings"
    echo "  sql <file>"
    echo ""
}

demarrer() {
    echo "Starting containers..."
    $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE up -d
    echo "Containers started"
    echo "Web:     http://localhost:8080"
    echo "Adminer: http://localhost:8082"
    composer_install
}

arreter() {
    echo "Stopping containers..."
    $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE down
    echo "Containers stopped"
}

redemarrer() {
    echo "Restarting containers..."
    $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE restart
    echo "Containers restarted"
}

reconstruire() {
    echo "Rebuilding containers..."
    $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE down -v
    $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE build
    $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE up -d
    echo "Containers rebuilt"
}

logs() {
    echo "Logs (Ctrl+C to stop)"
    $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE logs -f
}

status() {
    echo "Status:"
    $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE ps
}

nettoyer() {
    read -p "Remove containers and volumes? (oui/non): " confirm
    if [ "$confirm" = "oui" ]; then
        $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE down -v
        echo "Cleanup done"
    else
        echo "Canceled"
    fi
}

db_reset() {
    read -p "Reset database (data lost)? (oui/non): " confirm
    if [ "$confirm" = "oui" ]; then
        $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE down -v
        $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE up -d
        echo "Database reset"
    else
        echo "Canceled"
    fi
}

shell() {
    echo "Opening shell in web container..."
    $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE exec web bash
}

fix_permissions() {
    echo "Fixing permissions..."
    USER=$(whoami)
    
    # On Mac, don't use sudo for local files and use staff group
    if [ "$(uname -s)" = "Darwin" ]; then
        chown -R $USER:staff . 2>/dev/null || true
        find . -type d -exec chmod 755 {} \; 2>/dev/null || true
        find . -type f -exec chmod 644 {} \; 2>/dev/null || true
        chmod +x docker.sh podman.sh run_docker_mac.sh 2>/dev/null || true
    else
        chown -R $USER:$USER . 2>/dev/null || true
        find . -type d -exec chmod 755 {} \; 2>/dev/null || true
        find . -type f -exec chmod 644 {} \; 2>/dev/null || true
        chmod +x docker.sh podman.sh 2>/dev/null || true
    fi

    if [ -d "src/codeigniter/writable" ]; then
        chmod -R 777 src/codeigniter/writable 2>/dev/null || true
    fi

    if $SUDO podman ps --format '{{.Names}}' 2>/dev/null | grep -q "^sae_web$"; then
        if ! $SUDO podman exec sae_web chmod -R 777 /src/codeigniter/writable 2>/dev/null; then
            echo "chmod failed in container"
            exit 1
        fi
        # chown will fail with volumes, so make it optional
        $SUDO podman exec sae_web chown -R www-data:www-data /src/codeigniter/writable 2>/dev/null || true
    else
        echo "Container sae_web not running"
        echo "Start with: ./podman.sh demarrer"
        exit 1
    fi

    echo "Permissions fixed"
}

container_running() {
    local name="$1"
    $SUDO podman ps --format '{{.Names}}' 2>/dev/null | grep -q "^${name}$"
}

test_triggers() {
    echo "Running trigger tests..."
    $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE exec db \
        psql -U sae_user -d sae_database -f /docker-entrypoint-initdb.d/scripts/test_triggers.sql
}

test_procedures() {
    echo "Running procedure tests..."
    $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE exec db \
        psql -U sae_user -d sae_database -f /docker-entrypoint-initdb.d/scripts/test_procedures.sql
}

test_embeddings() {
    echo "Running embeddings tests..."
    $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE exec db \
        psql -U sae_user -d sae_database -f /docker-entrypoint-initdb.d/scripts/test_embeddings.sql
}

populate_embeddings() {
    echo "Generating embeddings..."
    $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE exec -T embeddings \
        python3 /app/populate_embeddings.py
}

run_sql() {
    if [ -z "$2" ]; then
        echo "Usage: ./podman.sh sql <file.sql>"
        exit 1
    fi

    SQL_FILE="$2"
    if [ ! -f "$SQL_FILE" ]; then
        echo "File not found: $SQL_FILE"
        exit 1
    fi

    if [[ "$SQL_FILE" == database/* ]]; then
        CONTAINER_PATH="/docker-entrypoint-initdb.d/${SQL_FILE#database/}"
    else
        echo "File must be under database/"
        exit 1
    fi

    echo "Running SQL: $SQL_FILE"
    $SUDO $COMPOSE_CMD --env-file $ENV_FILE -f $COMPOSE_FILE exec db \
        psql -U sae_user -d sae_database -f "$CONTAINER_PATH"
}

main() {
    echo "Using podman-compose"
    check_sudo
    case "$1" in
        demarrer) demarrer ;;
        arreter) arreter ;;
        redemarrer) redemarrer ;;
        reconstruire) reconstruire ;;
        logs) logs ;;
        status) status ;;
        nettoyer) nettoyer ;;
        db-reset) db_reset ;;
        shell) shell ;;
        permissions) fix_permissions ;;
        test-triggers) test_triggers ;;
        test-procedures) test_procedures ;;
        test-embeddings) test_embeddings ;;
        populate-embeddings) populate_embeddings ;;
        sql) run_sql "$@" ;;
        *) menu ;;
    esac
}

main "$@"