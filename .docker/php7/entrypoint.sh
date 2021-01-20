#!/bin/bash
. `pwd`/.env
if [ ! -d "vendor" ]; then
    export COMPOSER_ALLOW_SUPERUSER=1

    composer global require hirak/prestissimo

    composer install

    git clone --progress -b "${OJS_VERSION}" --single-branch --depth 1 --recurse-submodules -j 4 https://github.com/pkp/ojs
    cd ojs
    composer --working-dir=lib/pkp install
    composer --working-dir=plugins/paymethod/paypal install
    composer --working-dir=plugins/generic/citationStyleLanguage install
fi
php-fpm
