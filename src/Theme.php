<?php

namespace NgatNgay\WordPress;

use function NgatNgay\{request};

class Theme
{
    public static string $logo = '';

    public static function url() {
        return get_theme_file_uri();
    }

    public static function path() {
        return get_template_directory();
    }

    public static function getLogo(): string
    {
        $logoId = get_theme_mod('custom_logo');
        $logo = wp_get_attachment_image_src($logoId, 'full');

        if (has_custom_logo()) {
            return esc_url($logo[0]);
        } else {
            return self::$logo;
        }
    }
    public static function setDefaultLogo($logo): void
    {
        self::$logo = $logo;
    }

    public static function addUrl($key, $regexUrl, $callback): void
    {
        add_action('init', function () use ($key, $regexUrl) {
            add_rewrite_rule($regexUrl, 'index.php?ngatngay_rewrite=' . $key, 'top');
        });

        add_filter('query_vars', function ($query_vars) {
            $query_vars[] = 'ngatngay_rewrite';
            return $query_vars;
        });

        add_action('template_redirect', function () use ($key, $regexUrl, $callback) {
            if (get_query_var('ngatngay_rewrite') === $key) {
                $uri = ltrim(request()->getUri('request'), '/');

                preg_match('#' . $regexUrl . '#', $uri, $matches);
                call_user_func($callback, $matches);
            }
        });
    }
}
