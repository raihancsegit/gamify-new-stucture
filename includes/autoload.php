<?php
// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Gamify Autoloader.
 * This class handles autoloading of plugin classes based on a specific naming convention.
 * Convention:
 *   - Class Name: Gamify_Subfolder_ClassName (e.g., Gamify_Core_Loader)
 *   - File Name: class-gamify-subfolder-classname.php (e.g., class-gamify-core-loader.php)
 */
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

    /**
     * The main autoloading function.
     * @param string $class_name The name of the class to load.
     */
    private function autoload($class_name)
    {
        if (strpos($class_name, 'Gamify_') !== 0) {
            return;
        }

        // Convert class name to a filename, replacing underscores with hyphens.
        // Example: Gamify_Core_Loader -> gamify-core-loader
        $class_file = str_replace('_', '-', strtolower($class_name));

        // Split the class name to determine the subfolder.
        $parts = explode('_', $class_name);
        $subfolder = '';
        if (isset($parts[1])) {
            // We use the second part as the subfolder name.
            $subfolder = strtolower($parts[1]) . '/';
        }

        // Construct the full file path.
        // Example: includes/core/class-gamify-core-loader.php
        $file_path = GAMIFY_INCLUDES . $subfolder . 'class-' . $class_file . '.php';

        if (is_readable($file_path)) {
            require_once $file_path;
        }
    }
}

// Instantiate the autoloader.
Gamify_Autoload::instance();
