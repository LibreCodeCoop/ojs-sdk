<?php

require_once 'vendor/autoload.php';

chdir(getenv('OJS_WEB_BASEDIR'));

require_once './lib/pkp/tests/phpunit-bootstrap.php';

import('lib.pkp.classes.core.PKPRequest');
import('lib.pkp.classes.core.PKPRouter');
import('lib.pkp.classes.core.Registry');
$request = new \PKPRequest();
$router = new \PKPRouter();
$router->setApplication(\PKPApplication::get());
$request->setRouter($router);
\Registry::set('request', $request);
\AppLocale::$request = \Registry::get('request', true, null);