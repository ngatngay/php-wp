<?php

namespace wpx;

class seo
{
    public static function title($args)
    {
        if (is_string($args)) {
            $title = $args;
        }

        if (is_array($args)) {
            $args = array_merge([
                'title' => get_bloginfo('name'),
                'page' => 1,
                'total_page' => 1,
                'desc' => get_bloginfo('description'),
                'sep' => '-'
            ], $args);

            $args['sitename'] = $args['sitename'] ?? $args['title'];
            $args['sitedesc'] = $args['sitedesc'] ?? $args['desc'];

            if ($args['page'] > 1) {
                $args['page'] = sprintf(__('trang %s trên %s'), $args['page'], $args['total_page']);
            } else {
                $args['page'] = '';
            }

            $newArgs = [];
            foreach ($args as $key => $value) {
                $newArgs['%' . $key . '%'] = $value;
            }

            $format = get_option('rank-math-options-titles')['homepage_title'] ?? '%title% %sep% %sitedesc% %sep% %page%';
            $title = strtr($format, $newArgs);
        }

        add_filter('rank_math/frontend/title', function ($originTitle) use ($title) {
            return $title;
        }, 100);
    }
}
