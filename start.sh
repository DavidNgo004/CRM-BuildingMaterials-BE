#!/bin/sh
set -e

echo "=== CRM VLXD Backend Startup ==="

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "[WARN] APP_KEY is not set. Generating..."
    php artisan key:generate --force
fi

# Clear old cached config (important when env vars change)
echo "[1/5] Clearing config cache..."
php artisan config:clear

# Run database migrations
echo "[2/5] Running migrations..."
php artisan migrate --force

# Seed admin account (uses firstOrCreate, safe to run multiple times)
echo "[2.5/5] Seeding admin account..."
php artisan db:seed --class=AdminSeeder --force

# Create storage symlink if needed
echo "[3/5] Linking storage..."
php artisan storage:link --quiet 2>/dev/null || true

# Cache config and routes for performance
echo "[4/5] Caching config & routes..."
php artisan config:cache
php artisan route:cache

# Start the server
echo "[5/5] Starting server on port ${PORT:-10000}..."
PHP_CLI_SERVER_WORKERS=4 php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
