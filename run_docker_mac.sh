#!/bin/bash

COMPOSE_FILE="docker/docker-compose.yml"
ENV_FILE=".env"

echo "🔍 Checking Docker Desktop installation..."

# 1. Vérifie si Docker Desktop est installé
if [ ! -d "/Applications/Docker.app" ]; then
    echo "❌ Docker Desktop is not installed. Please install it from https://www.docker.com/products/docker-desktop/"
    exit 1
fi

echo "✓ Docker Desktop is installed."

# 2. Vérifie si Docker daemon tourne
echo "🔍 Checking Docker daemon..."

if ! docker info >/dev/null 2>&1; then
    echo "⚠️ Docker daemon is not running. Starting Docker Desktop..."

    # Lance Docker Desktop
    open -a Docker

    # Attente que le daemon soit prêt
    echo "⏳ Waiting for Docker to start..."
    while ! docker info >/dev/null 2>&1; do
        sleep 2
    done

    echo "✓ Docker daemon is now running."
else
    echo "✓ Docker daemon already running."
fi

# 3. Vérifie docker compose (new syntax, bundled with Docker Desktop)
echo "🔍 Checking docker compose..."
if ! docker compose version &>/dev/null; then
    echo "❌ docker compose not available."
    echo "➡️ Update Docker Desktop or run: brew install docker-compose"
    exit 1
fi

echo "✓ docker compose available."

# 4. Vérifie que les fichiers existent
if [ ! -f "$COMPOSE_FILE" ]; then
    echo "❌ Cannot find compose file: $COMPOSE_FILE"
    exit 1
fi

if [ ! -f "$ENV_FILE" ]; then
    echo "❌ Cannot find env file: $ENV_FILE"
    exit 1
fi

# 5. Lance docker compose
echo "🚀 Starting docker compose..."
docker compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" up -d --build

echo "🎉 Done! Containers are running."

