<?php
if (! defined('ABSPATH')) exit;

final class Gamify_Autoload
{
    private static $instance = null;

    private function __construct()
    {
        spl_autoload_register([$this, 'autoload']);
    }

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function autoload($class)
    {
        // We only autoload classes from our 'Gamify' namespace.
        if (0 !== strpos($class, 'Gamify\\')) {
            return;
        }

        // Convert Namespace to file path.
        // Example: Gamify\System\Points -> includes/system/points.php
        $filename = strtolower(
            preg_replace(
                ['/^Gamify\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/'],
                ['', '$1-$2', '-', DIRECTORY_SEPARATOR],
                $class
            )
        );

        $file = GAMIFY_INCLUDES . $filename . '.php';

        if (is_readable($file)) {
            require_once $file;
        }
    }
}
Gamify_Autoload::instance();
