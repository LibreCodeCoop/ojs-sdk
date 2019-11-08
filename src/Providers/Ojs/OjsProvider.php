<?php
namespace OjsSdk\Providers\Ojs;

class OjsProvider
{
    /**
     * @return \Application
     */
    public static function getApplication()
    {
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
        chdir($currentDirectory);
        return $application;
    }
}
