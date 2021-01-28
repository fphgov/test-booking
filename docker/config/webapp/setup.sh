#!/bin/sh

unset MODE

while getopts 'm:' c
do
  case $c in
    m) MODE="$OPTARG" ;;
  esac
done

if [ $MODE = "development" ]; then
  cd /var/www/html

  php -d allow_url_fopen=on /usr/local/bin/composer install

  php ./bin/load_font.php Arial ./fonts/arial.ttf ./fonts/arialbd.ttf ./fonts/arialbi.ttf ./fonts/arialbi.ttf ./fonts/arialbi.ttf ./fonts/ariali.ttf ./fonts/ARIALN.TTF ./fonts/ARIALNB.TTF ./fonts/ARIALNBI.ttf ./fonts/ARIALNI.TTF

  composer db-update
fi

if [ $MODE = "production" ]; then
  cd /var/www/html

  php -d allow_url_fopen=on /usr/local/bin/composer install --no-ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader

  php ./bin/load_font.php Arial ./fonts/arial.ttf ./fonts/arialbd.ttf ./fonts/arialbi.ttf ./fonts/arialbi.ttf ./fonts/arialbi.ttf ./fonts/ariali.ttf ./fonts/ARIALN.TTF ./fonts/ARIALNB.TTF ./fonts/ARIALNBI.ttf ./fonts/ARIALNI.TTF

  composer db-update
fi
