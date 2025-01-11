<?php

namespace NgatNgay\WordPress;

use function NgatNgay\{request};

class Theme
{
    // logo url
    public static $logo = '';
    
    public static function getLogo()
    {
        $logoId = get_theme_mod('custom_logo');
        $logo = wp_get_attachment_image_src($logoId, 'full');

        if (has_custom_logo()) {
            return esc_url($logo[0]);
        } else {
            return self::$logo;
        }
    }
    public static function setLogo($logo) {
        self::$logo = $logo;
    }
    
    public static function addUrl($key, $regexUrl, $callback)
    {
        add_action('init', function() use ($key, $regexUrl) {
            add_rewrite_rule($regexUrl, 'index.php?ngatngay_rewrite=' . $key, 'top');
        });

        add_filter('query_vars', function ($query_vars) {
            $query_vars[] = 'ngatngay_rewrite';
            return $query_vars;
        });

        add_action('template_redirect', function () use($key, $regexUrl, $callback) {
            if (get_query_var('ngatngay_rewrite') === $key) {
                $uri = ltrim(request()->getUri('request'), '/');
                
                preg_match('#' . $regexUrl . '#', $uri, $matches);
                call_user_func($callback, $matches);
            }
        });
    }
}
