#!/bin/bash

# Task App - Automated Deployment Script
# Usage: ./deploy.sh

set -e

echo "==== START DEPLOY ===="

# 1. Setup Environment & Volume (Bootstrap)
# Only runs if expecting a fresh setup or ensuring resilience
export $(grep -v '^#' .env | xargs)
DB_VOLUME=${DB_VOLUME_NAME:-task-app-dbdata}
docker volume create "$DB_VOLUME" >/dev/null 2>&1 || true

# 2. Pull Latest Images
echo "⬇️  Pulling latest images..."
docker compose pull

# 3. Start Containers
echo "🔥 Restarting containers..."
docker compose up -d

# 4. Wait for Database (Resilience)
echo "⏳ Waiting for Database (up to 120s)..."
DB_READY=false
for i in {1..60}; do
    if docker compose exec db mysqladmin ping -uroot -proot --silent >/dev/null 2>&1; then
        DB_READY=true
        echo -e "\n✅ Database is ready!"
        echo "💤 Waiting 10s for initialization to stabilize..."
        sleep 10
        break
    fi
    echo -n "."
    sleep 2
done

if [ "$DB_READY" = false ]; then
    echo -e "\n❌ Database failed to start within timeout."
    exit 1
fi

# 5. Run Migrations
echo "📦 Running Migrations..."
docker compose exec app php artisan migrate --force

# 6. Cleanup (Match Production)
echo "🧹 Cleaning unused images..."
docker image prune -f

echo "==== DEPLOY COMPLETE ===="
echo "👉 App Deployed! Access it via: http://${APP_HOST:-localhost}:${APP_PORT:-8100}"
