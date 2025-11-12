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
        // We only handle classes with the 'Gamify_' prefix.
        if (strpos($class_name, 'Gamify_') !== 0) {
            return;
        }

        // Convert the class name to a filename.
        // 1. Replace underscores with hyphens.
        // 2. Convert to lowercase.
        // Example: Gamify_Core_Loader -> gamify-core-loader
        $file_name = str_replace('_', '-', strtolower($class_name));

        // Split the class name to determine the subfolder.
        // Example: Gamify_Core_Loader -> ['Gamify', 'Core', 'Loader']
        $parts = explode('_', $class_name);

        // The second part of the class name (e.g., 'Core', 'Admin', 'API', 'System') is the subfolder.
        // If there's no second part (e.g., class Gamify_Something), it looks in the 'includes' root.
        $subfolder = '';
        if (isset($parts[1])) {
            // Convert subfolder name to lowercase.
            $subfolder = strtolower($parts[1]) . '/';
        }

        // Construct the full file path.
        // Example: includes/core/class-gamify-core-loader.php
        $file_path = GAMIFY_INCLUDES . $subfolder . 'class-' . $file_name . '.php';

        if (is_readable($file_path)) {
            require_once $file_path;
        }
    }
}

// Instantiate the autoloader.
Gamify_Autoload::instance();
