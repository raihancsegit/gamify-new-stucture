<?php
// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Handles plugin installation tasks like creating database tables.
 */
class Gamify_Core_Installer
{

    /**
     * Run the installer.
     */
    public function run()
    {
        $this->create_tables();
        $this->insert_default_data();
    }

    /**
     * Create all necessary database tables for the plugin.
     */
    private function create_tables()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Include the upgrade.php file for dbDelta function.
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // SQL for points log table
        $sql_points = "CREATE TABLE {$wpdb->prefix}gamify_points (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) NOT NULL,
            points INT(11) NOT NULL,
            context VARCHAR(100) NOT NULL,
            reference_id BIGINT(20) DEFAULT NULL,
            description TEXT,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql_points);

        // SQL for main logs table
        $sql_logs = "CREATE TABLE {$wpdb->prefix}gamify_logs (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) NOT NULL,
            trigger_key VARCHAR(255) NOT NULL,
            trigger_type ENUM('system','manual','schedule') NOT NULL,
            event_name VARCHAR(255) NOT NULL,
            status ENUM('success','failed','skipped') NOT NULL,
            message TEXT,
            reference_id BIGINT(20) DEFAULT NULL,
            meta JSON,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY trigger_key (trigger_key)
        ) $charset_collate;";
        dbDelta($sql_logs);

        // SQL for point types table
        $sql_point_types = "CREATE TABLE {$wpdb->prefix}gamify_point_types (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            plural_name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql_point_types);

        // SQL for triggers configuration table
        $sql_triggers = "CREATE TABLE {$wpdb->prefix}gamify_triggers (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            trigger_key VARCHAR(255) NOT NULL,
            points_to_award INT(11) NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            log_description VARCHAR(255) DEFAULT '',
            PRIMARY KEY (id),
            UNIQUE KEY trigger_key (trigger_key)
        ) $charset_collate;";
        dbDelta($sql_triggers);
    }

    /**
     * Insert default data into tables on first activation.
     */
    private function insert_default_data()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gamify_point_types';

        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        if ($count > 0) {
            return; // Data already exists.
        }

        $wpdb->insert($table_name, ['name' => 'Coin', 'plural_name' => 'Spark Points', 'slug' => 'coin']);
        $wpdb->insert($table_name, ['name' => 'Token', 'plural_name' => 'Skill Tokens', 'slug' => 'token']);
        $wpdb->insert($table_name, ['name' => 'XP', 'plural_name' => 'Power Gems', 'slug' => 'xp']);
    }
}
