<?php
namespace OjsSdk\Providers\Ojs;

use PluginRegistry;

class OjsProvider
{
    private static $loaded = false;
    /**
     * @return \Application
     */
    public static function getApplication()
    {
        if (self::$loaded) {
            return;
        }
        self::$loaded = true;
        define('SESSION_DISABLE_INIT', true);
        define('INDEX_FILE_LOCATION', getenv('WEB_DOCUMENT_ROOT') .'/ojs/index.php');
        define('ENV_SEPARATOR', strtolower(substr(PHP_OS, 0, 3)) == 'win' ? ';' : ':');
        if (!defined('DIRECTORY_SEPARATOR')) {
            // Older versions of PHP do not define this
            define('DIRECTORY_SEPARATOR', strtolower(substr(PHP_OS, 0, 3)) == 'win' ? '\\' : '/');
        }
        define('BASE_SYS_DIR', dirname(INDEX_FILE_LOCATION));
        $currentDirectory = getcwd();
        chdir(BASE_SYS_DIR);

        // Update include path - for backwards compatibility only
        // Try to use absolute (/...) or relative (./...) filenames
        // wherever possible to bypass the costly file name normalization
        // process.
        ini_set(
            'include_path',
            ENV_SEPARATOR . BASE_SYS_DIR
            . ENV_SEPARATOR . BASE_SYS_DIR . '/classes'
            . ENV_SEPARATOR . BASE_SYS_DIR . '/pages'
            . ENV_SEPARATOR . BASE_SYS_DIR . '/lib/pkp'
            . ENV_SEPARATOR . BASE_SYS_DIR . '/lib/pkp/classes'
            . ENV_SEPARATOR . BASE_SYS_DIR . '/lib/pkp/pages'
            . ENV_SEPARATOR . BASE_SYS_DIR . '/lib/pkp/lib/adodb'
            . ENV_SEPARATOR . ini_get('include_path')
        );
        // System-wide functions
        require('includes/functions.inc.php');

        // Initialize the application environment
        import('classes.core.Application');
        $application = new \Application();
        import('lib.pkp.classes.core.PKPRequest');
        import('lib.pkp.classes.core.PKPRouter');
        import('lib.pkp.classes.core.Registry');
        $request = new \PKPRequest();
        $router = new \PKPRouter();
        $router->setApplication($application);
        $request->setRouter($router);
        \Registry::set('request', $request);
        \AppLocale::$request = \Registry::get('request', true, null);
        $request = \AppLocale::$request;
        $plugins = PluginRegistry::loadCategory('generic', true, 1);
        chdir($currentDirectory);
        return $application;
    }
}
