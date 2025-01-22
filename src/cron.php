<?php

namespace wpx;

class cron {
    public static function init() {
        add_filter('cron_schedules', function($schedules) { 
            $schedules['monthly'] = array(
                'interval' => 60 * 60 * 24 * 30,
                'display'  => esc_html__( 'Every Month' )
            );
            return $schedules;
        });
    }
    
    public static function add(
        string $key,
        string|callable $hook,
        array $option
    ) {
        $key = "my_cron_$key";
        $option = array_merge([
            'start' => time(),
            'hook_args' => [],
            'wp_err' => true
        ], $option);
    
        if (!is_string($hook)) {
            add_action($key, $hook);
            $hook = $key;
        }
        
        if (!wp_next_scheduled($key)) {
            wp_schedule_event($option['start'], $option['repeat'], $hook, $option['hook_args'], $option['wp_err']);
        }
    }
    
}

// init
cron::init();
