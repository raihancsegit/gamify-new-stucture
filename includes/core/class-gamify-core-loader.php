<?php
if (! defined('ABSPATH')) {
    exit;
}

final class Gamify_Core_Loader
{
    private static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        // Autoloader will load these files when the classes are instantiated.
        new Gamify_Admin_Menu();
        new Gamify_API_Manager();
        new Gamify_System_Trigger_Engine();
    }
}
