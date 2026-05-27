#!/bin/bash

bold=$(tput bold)
normal=$(tput sgr0)

if [[ $1 == "--rebuild" ]]; then
    vendor/bin/sail down -v --remove-orphans
fi

if [ ! -f ./.env ]; then
    echo -e "\n${bold}> cp ./.env.example ./.env${normal}"
    cp ./.env.example ./.env
fi

echo -e "\n${bold}> composer install${normal}"
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    composer:latest \
    composer install --ignore-platform-reqs --no-scripts

echo -e "\n${bold}> sail build${normal}"
vendor/bin/sail build

echo -e "\n${bold}> sail up -d${normal}"
vendor/bin/sail up -d

if [ ! -f ./config/octane.php ] || [ ! -f ./frankenphp ]; then
    echo -e "\n${bold}> sail artisan octane:install --server=frankenphp${normal}"
    [ -f ./config/octane.php ] && cp ./config/octane.php ./config/octane.php.bak
    vendor/bin/sail artisan octane:install --server=frankenphp --force
    [ -f ./config/octane.php.bak ] && mv ./config/octane.php.bak ./config/octane.php
fi

echo -e "\n${bold}> sail artisan storage:link${normal}"
vendor/bin/sail artisan storage:link

source .env
if [ "$APP_KEY" == "" ]; then
    echo -e "\n${bold}> sail artisan key:generate${normal}"
    vendor/bin/sail artisan key:generate
fi

if [ "$TELEGRAM_TOKEN" == "" ]; then
    echo -e "\n${bold}Enter your Telegram Bot Token (leave empty to skip):${normal}"
    read -r telegram_token
    if [ "$telegram_token" != "" ]; then
        sed -i '' "s/^TELEGRAM_TOKEN=.*/TELEGRAM_TOKEN=$telegram_token/" .env
        echo -e "TELEGRAM_TOKEN set."
    fi
fi

if [ "$OPENROUTER_API_KEY" == "" ]; then
    echo -e "\n${bold}Enter your OpenRouter API Key (leave empty to skip):${normal}"
    read -r openrouter_api_key
    if [ "$openrouter_api_key" != "" ]; then
        sed -i '' "s/^OPENROUTER_API_KEY=.*/OPENROUTER_API_KEY=$openrouter_api_key/" .env
        echo -e "OPENROUTER_API_KEY set."
    fi
fi

echo -e "\n${bold}> sail artisan migrate:fresh --seed${normal}"
vendor/bin/sail artisan migrate:fresh --seed --force --no-interaction

echo -e "\n${bold}> sail bun install${normal}"
vendor/bin/sail bun install

echo -e "\n${bold}> sail bun run build${normal}"
vendor/bin/sail bun run build

# echo -e "\n${bold}> sail bunx playwright install${normal}"
# vendor/bin/sail bunx playwright install

echo -e "\n${bold}Done!${normal}"
echo -e "You can now access your project at http://localhost"
echo -e "To start tracking habits immediately go to http://localhost/app/track"

echo -e "\nDon't forget to run the command for all your development needs:"
echo -e "${bold}sail composer dev${normal}"
