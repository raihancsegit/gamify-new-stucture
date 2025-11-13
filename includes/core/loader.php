<?php

namespace Gamify\Core;

if (! defined('ABSPATH')) exit;

final class Loader
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
        new \Gamify\Admin\Menu();
        new \Gamify\API\Manager();
        new \Gamify\System\Triggers();
    }
}
