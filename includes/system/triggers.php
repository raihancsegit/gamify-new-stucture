<?php

namespace Gamify\System;

if (! defined('ABSPATH')) exit;

class Triggers
{
    public function __construct()
    {
        add_action('wp_login', [$this, 'on_user_login'], 10, 2);
    }

    public function on_user_login($user_login, $user)
    {
        // Call the global helper function with a backslash.
        \gamify_add_points($user->ID, 10, 'user_login');
    }
}
