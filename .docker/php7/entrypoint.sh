#!/bin/bash
. `pwd`/.env
if [ ! -d "vendor" ]; then
    export COMPOSER_ALLOW_SUPERUSER=1

    composer global require hirak/prestissimo

    composer install

    git clone --progress -b "${OJS_VERSION}" --single-branch --depth 1 --recurse-submodules -j 4 https://github.com/pkp/ojs
    composer --working-dir=ojs/lib/pkp install
    composer --working-dir=ojs/plugins/paymethod/paypal install
    composer --working-dir=ojs/plugins/generic/citationStyleLanguage install
    php tests/setupOjs.php
fi
php-fpm
