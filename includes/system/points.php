<?php

namespace Gamify\API;

if (! defined('ABSPATH')) exit;

class Manager
{
    protected $namespace = 'gamify/v1';

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }
    public function register_routes()
    { /* Your register_rest_route calls */
    }
}
