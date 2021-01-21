<?php

namespace OjsSdk\Providers;

class Provider
{
    private static $instance = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            $className = get_called_class();
            self::$instance = new $className();
        }

        return self::$instance;
    }
}
