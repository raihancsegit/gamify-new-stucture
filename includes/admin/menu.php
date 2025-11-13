<?php

namespace Gamify\Admin;

if (! defined('ABSPATH')) exit;

class Menu
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function register()
    {
        add_menu_page('Gamify', 'Gamify', 'manage_options', 'gamify', [$this, 'render_app'], 'dashicons-star-filled', 25);
    }

    public function render_app()
    {
        echo '<div id="gamify-admin-app"></div>';
    }

    public function enqueue_assets($hook)
    {
        if ('toplevel_page_gamify' !== $hook) return;
    }
}
