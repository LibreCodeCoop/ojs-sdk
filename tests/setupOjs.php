<?php

chdir(getenv('OJS_WEB_BASEDIR'));
if (file_exists(getenv('OJS_WEB_BASEDIR') . '/config.inc.php')) {
    return;
}
copy('config.TEMPLATE.inc.php', 'config.inc.php');
require('tools/bootstrap.inc.php');

import('lib.pkp.classes.cliTool.InstallTool');

class OJSInstallTest extends InstallTool {
	/**
	 * Constructor.
	 * @param $argv array command-line arguments
	 */
	function __construct(array $params) {
        parent::__construct();
        $this->params = $params;
	}
}

$params = [
    'locale' => 'en_US',
    'additionalLocales' => [
        'en_US'
    ],
    'clientCharset' => 'utf-8',
    'connectionCharset' => 'utf8',
    'databaseCharset' => 'utf8',
    'filesDir' => '/app/ojs/',
    'adminUsername' => 'admin',
    'adminPassword' => 'admin',
    'adminPassword2' => 'admin',
    'adminEmail' => 'admin@test.coop',
    'databaseDriver' => 'mysqli',
    'databaseHost' => 'db',
    'databaseUsername' => 'root',
    'databasePassword' => 'root',
    'databaseName' => 'ojs',
    'createDatabase' => 1,
    'oaiRepositoryId' => 'localhost',
    'enableBeacon' => 1,
    'install' => 1
];

$tool = new OJSInstallTest($params);
$tool->install();