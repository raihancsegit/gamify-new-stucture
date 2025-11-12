<?php

/**
 * Plugin Name:       Gamify
 * Description:       A modern gamification plugin for WordPress.
 * Version:           1.0.0
 * Author:            Kodezen
 * Text Domain:       gamify
 */

if (! defined('ABSPATH')) {
    exit;
}
if (class_exists('Gamify')) {
    return;
}

final class Gamify
{
    private static $instance = null;

    private function __construct()
    {
        $this->define_constants();
        require_once GAMIFY_INCLUDES . 'autoload.php';
        require_once GAMIFY_INCLUDES . 'gamify-functions.php';
        register_activation_hook(GAMIFY_FILE, [$this, 'activate']);
        add_action('plugins_loaded', [$this, 'on_plugins_loaded']);
    }

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function define_constants()
    {
        define('GAMIFY_VERSION', '1.0.0');
        define('GAMIFY_FILE', __FILE__);
        define('GAMIFY_PATH', plugin_dir_path(__FILE__));
        define('GAMIFY_URL', plugin_dir_url(__FILE__));
        define('GAMIFY_INCLUDES', GAMIFY_PATH . 'includes/');
    }

    public function activate()
    {
        new Gamify_Core_Installer();
    }

    public function on_plugins_loaded()
    {
        Gamify_Loader::instance();
    }
}

function gamify_start()
{
    return Gamify::instance();
}
gamify_start();
