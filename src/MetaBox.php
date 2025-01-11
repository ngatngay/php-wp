<?php

namespace NgatNgay\WordPress;

class MetaBox
{
    private static $prefix = 'pe_';

    public static function add($args)
    {
        // server
        add_action('save_post', $args['process']);

        $prefix = self::$prefix . $args['id'];
        add_action('add_meta_boxes', function () use ($prefix, $args) {
            add_meta_box(
                $prefix,
                $args['name'],
                $args['render'],
                'post'
            );
        });
    }
}
