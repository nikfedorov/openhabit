#!/usr/bin/env bash

set -e

if ! command -v docker &>/dev/null; then
    echo "Error: docker is not installed or not in PATH." >&2
    exit 1
fi

if ! docker compose version &>/dev/null; then
    echo "Error: docker compose plugin is not available." >&2
    exit 1
fi

COMPOSE_FILE="compose.prod.yaml"
ARTISAN="docker compose -f $COMPOSE_FILE exec app php artisan"
bold=$(tput bold 2>/dev/null || echo '')
normal=$(tput sgr0 2>/dev/null || echo '')

step() { echo -e "\n${bold}> $*${normal}"; }

# sed -i compatible with macOS and Linux
sedi() {
    if [[ "$(uname)" == "Darwin" ]]; then sed -i '' "$@"; else sed -i "$@"; fi
}

# ── .env (first run only) ──────────────────────────────────────────────────────

FIRST_RUN=false

if [ ! -f .env ]; then
    FIRST_RUN=true
    cp .env.example .env
    sedi "s/APP_ENV=local/APP_ENV=production/" .env
    sedi "s/APP_DEBUG=true/APP_DEBUG=false/" .env
    sedi "s/LOG_LEVEL=debug/LOG_LEVEL=error/" .env

    while true; do
        echo -e "\n${bold}App URL (e.g. https://example.com):${normal}"
        read -r v
        [[ "$v" == https://* ]] && break
        echo "URL must start with https://"
    done
    sedi "s|APP_URL=http://localhost|APP_URL=$v|" .env

    db_password=$(openssl rand -base64 24 | tr -d '/+=' | head -c 32)
    sedi "s/DB_PASSWORD=password/DB_PASSWORD=$db_password/" .env
    echo -e "Database password generated (see .env)."

    echo -e "\n${bold}Telegram Bot Token:${normal}"
    read -r v && sedi "s/^TELEGRAM_TOKEN=.*/TELEGRAM_TOKEN=$v/" .env

    echo -e "\n${bold}OpenRouter API Key (leave empty to skip):${normal}"
    read -r v
    if [ -n "$v" ]; then
        sedi "s/^OPENROUTER_API_KEY=.*/OPENROUTER_API_KEY=$v/" .env
        echo -e "OPENROUTER_API_KEY set."
    fi
fi

source .env
export WWWUSER="${WWWUSER:-$(id -u)}"
export WWWGROUP="${WWWGROUP:-$(id -g)}"

# ── Code & dependencies ────────────────────────────────────────────────────────

[ -d .git ] && git rev-parse --abbrev-ref '@{u}' &>/dev/null && { step "git pull"; git pull --ff-only; }

step "composer install --no-dev --optimize-autoloader"
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html \
    composer:latest composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-scripts --no-interaction

# ── Build & start ──────────────────────────────────────────────────────────────

step "docker compose build"
docker compose -f "$COMPOSE_FILE" build --build-arg WWWGROUP="$WWWGROUP"

step "docker compose up -d"
docker compose -f "$COMPOSE_FILE" up -d && sleep 5

# ── Octane / FrankenPHP ────────────────────────────────────────────────────────

if [ ! -f ./config/octane.php ] || [ ! -f ./frankenphp ]; then
    step "artisan octane:install --server=frankenphp"
    [ -f ./config/octane.php ] && cp ./config/octane.php ./config/octane.php.bak
    $ARTISAN octane:install --server=frankenphp --force
    [ -f ./config/octane.php.bak ] && mv ./config/octane.php.bak ./config/octane.php
fi

# ── Laravel setup ──────────────────────────────────────────────────────────────

[ -z "$APP_KEY" ] && { step "artisan key:generate"; $ARTISAN key:generate --force; }

$ARTISAN storage:link --force

if [ "$FIRST_RUN" = true ] || [[ "$1" == "--fresh" ]]; then
    step "artisan migrate"
    $ARTISAN migrate --force --no-interaction
    step "artisan db:seed"
    $ARTISAN db:seed --force --no-interaction
    step "artisan app:setup"
    $ARTISAN app:setup
    step "artisan app:generate-invoice-links"
    $ARTISAN app:generate-invoice-links
else
    step "artisan migrate"
    $ARTISAN migrate --force --no-interaction
fi

step "artisan config:cache && event:cache && route:cache && view:cache"
$ARTISAN config:cache && $ARTISAN event:cache && $ARTISAN route:cache && $ARTISAN view:cache

# ── Frontend ───────────────────────────────────────────────────────────────────

step "bun install && bun run build"
docker compose -f "$COMPOSE_FILE" exec app bun install --frozen-lockfile
docker compose -f "$COMPOSE_FILE" exec app bun run build

# ── Restart workers ────────────────────────────────────────────────────────────

step "restarting workers"
$ARTISAN horizon:terminate || true
docker compose -f "$COMPOSE_FILE" restart app

echo -e "\n${bold}Done! ${APP_URL:-http://localhost}${normal}"
