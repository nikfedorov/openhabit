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
    composer install --ignore-platform-reqs

echo -e "\n${bold}> sail build${normal}"
vendor/bin/sail build

echo -e "\n${bold}> sail up -d${normal}"
vendor/bin/sail up -d

echo -e "\n${bold}> sail artisan storage:link${normal}"
vendor/bin/sail artisan storage:link

source .env
if [ "$APP_KEY" == "" ]; then
    echo -e "\n${bold}> sail artisan key:generate${normal}"
    vendor/bin/sail artisan key:generate
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
echo -e "\nDon't forget to run the command for all your development needs:"
echo -e "${bold}sail composer dev${normal}"
