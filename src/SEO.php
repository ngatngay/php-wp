<?php

namespace NgatNgay\WordPress;

class SEO
{
    public static function pageNumberTitle($page, $query = null)
    {
        add_filter('rank_math/frontend/title', function ($title) use ($page, $query) {
            if ($page > 1) {
                return sprintf(
                    '%s - %s - %s',
                    get_the_title(),
                    'Trang ' . $page . ' trên ' . $query->max_num_pages,
                    get_bloginfo('description')
                );
            }
            return $title;
        });
    }
}
