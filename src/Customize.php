<?php

namespace NgatNgay\WordPress;

class Customize
{
    public static function addSection($key, $opt)
    {
        add_action('customize_register', function ($wp_customize) use ($key, $opt) {
            $wp_customize->add_section($key, $opt);
        });
    }

    public static function addControl($key, $opt)
    {
        add_action('customize_register', function ($wp_customize) use ($key, $opt) {
            $wp_customize->add_setting($key);
            $wp_customize->add_control($key, $opt);
        });
    }
}
