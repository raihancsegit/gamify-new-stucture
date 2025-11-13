<?php

namespace Gamify\Core;

if (! defined('ABSPATH')) exit;

class Installer
{
    public function __construct()
    {
        $this->create_tables();
    }
    private function create_tables()
    { /* Your DB creation SQL here */
    }
}
