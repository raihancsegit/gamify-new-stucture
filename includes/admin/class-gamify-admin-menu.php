<?php
// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Handles the creation of admin menus and enqueuing of scripts for the React app.
 */
class Gamify_Admin_Menu
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    /**
     * Register the main admin menu and submenus for the plugin.
     */
    public function register_admin_menu()
    {
        add_menu_page('Gamify', 'Gamify', 'manage_options', 'gamify', [$this, 'render_admin_app'], 'dashicons-star-filled', 20);
        add_submenu_page('gamify', 'Dashboard', 'Dashboard', 'manage_options', 'gamify', [$this, 'render_admin_app']);
        add_submenu_page('gamify', 'Points System', 'Points System', 'manage_options', 'gamify-points', [$this, 'render_admin_app']);
        add_submenu_page('gamify', 'Logs', 'Logs', 'manage_options', 'gamify-logs', [$this, 'render_admin_app']);
        add_submenu_page('gamify', 'Settings', 'Settings', 'manage_options', 'gamify-settings', [$this, 'render_admin_app']);
    }

    /**
     * Render the root div for the React application.
     */
    public function render_admin_app()
    {
        echo '<style>#gamify-admin-app{opacity:0;transition:opacity .3s ease-in-out}.gamify-app-loaded #gamify-admin-app{opacity:1}.gamify-loader{display:flex;justify-content:center;align-items:center;height:200px;font-size:1.2rem;color:#555}</style>';
        echo '<div id="gamify-admin-app"><div class="gamify-loader">Loading App...</div></div>';
    }

    /**
     * Enqueue scripts and styles for the React admin panel.
     */
    public function enqueue_admin_scripts($hook)
    {
        $pages = [
            'toplevel_page_gamify',
            'gamify_page_gamify-points',
            'gamify_page_gamify-logs',
            'gamify_page_gamify-settings'
        ];

        if (! in_array($hook, $pages)) {
            return;
        }

        // Hide all admin notices on our pages for a cleaner UI.
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');

        // Enqueue the main stylesheet.
        wp_enqueue_style(
            'gamify-admin-style',
            GAMIFY_URL . 'admin/dist/assets/main.css',
            [],
            filemtime(GAMIFY_PATH . 'admin/dist/assets/main.css')
        );

        // Enqueue the main script.
        wp_enqueue_script(
            'gamify-admin-script',
            GAMIFY_URL . 'admin/dist/assets/main.js',
            ['wp-element'],
            filemtime(GAMIFY_PATH . 'admin/dist/assets/main.js'),
            true
        );

        // Pass data from PHP to our React app.
        wp_localize_script(
            'gamify-admin-script',
            'gamifyApiSettings',
            ['nonce' => wp_create_nonce('wp_rest')]
        );
    }
}
