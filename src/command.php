<?php

namespace wpx;

class command
{
    public static function add($name, $callable, $args = [])
    {
        if ((defined('WP_CLI') && WP_CLI) || PHP_SAPI == 'cli') {
            \WP_CLI::add_command($name, $callable, $args);
        }
    }
}
