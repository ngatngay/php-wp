<?php

namespace wpx;

class metabox
{
    private static string $prefix = 'pe_';

    public static function add(array $args)
    {
        $prefix = self::$prefix . $args['id'];

        add_action('save_post', $args['execute']);
        add_action('add_meta_boxes', function () use ($prefix, $args) {
            add_meta_box(
                $prefix,
                $args['name'],
                $args['display'],
                'post'
            );
        });
    }
}
