<?php

namespace wpx;

class customize
{
    public static function add_section($key, $opt)
    {
        add_action('customize_register', function ($wp_customize) use ($key, $opt) {
            $wp_customize->add_section($key, $opt);
        });
    }

    public static function add_control($key, $opt)
    {
        add_action('customize_register', function ($wp_customize) use ($key, $opt) {
            $wp_customize->add_setting($key);
            $wp_customize->add_control($key, $opt);
        });
    }
}
