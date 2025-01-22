<?php

namespace wpx;

use NgatNgay\Http\Curl;

class post {
    private static $thumbnail = '';
    
    public static function get_id_by_slug(string $slug, string $type = 'post')
    {
        $post = get_page_by_path($slug, OBJECT, $type);

        return $post ? $post->ID : 0;
    }

    public static function insert_or_update($data)
    {
        if ($data['ID'] === 0) {
            $post = wp_insert_post($data, true);
        } else {
            $post = wp_update_post($data, true);
        }

        if (is_wp_error($post)) {
            \WP_CLI::log($post->get_error_message());
        }

        return $post;
    }

    public static function get_random($limit = 10)
    {
        return get_posts([
            'posts_per_page' => $limit,
            'orderby' => 'rand'
        ]);
    }

    public static function get_thumbnail_url($post = null) {
        if (has_post_thumbnail($post)) {
            return get_the_post_thumbnail_url();
        } else {
            return self::$thumbnail;
        }
    }
    
    public static function set_default_thumbnail(string $thumbnail) {
        self::$thumbnail = $thumbnail;
    }
    
    public static function set_thumbnail_url($post, $thumbUrl)
    {
        $curl = new Curl();
        $postData = get_post($post);
        $uploadDir = wp_upload_dir(date('Y/m', strtotime($postData->post_date)));
        $url = $uploadDir['url'] . '/' . $post . '-' . basename($thumbUrl);
        $path = $uploadDir['path'] . '/' . $post . '-' . basename($thumbUrl);

        if (!$curl->download($thumbUrl, $path)) {
            return;
        }

        $attachment = [
            'ID' => get_post_thumbnail_id($post),
            'post_mime_type' => mime_content_type($path),
            'post_title' => $postData->post_title
        ];
        $attachId = wp_insert_attachment($attachment, $path);
        $attachData = wp_generate_attachment_metadata($attachId, $path);

        wp_update_attachment_metadata($attachId, $attachData);
        set_post_thumbnail($post, $attachId);
    }

    /**
     * @param int $post
     * @param string $taxonomy
     * @return array|bool|\WP_Error
     */
    public static function get_terms(int $post, string $taxonomy)
    {
        $res = get_the_terms($post ?: get_the_ID(), $taxonomy);
        return $res ?: [];
    }
    // view
    public static function get_view(int $postId = 0, string $type = '')
    {
        $key = $type ? "view_$type" : 'view';
        return (int) get_post_meta($postId ?: get_the_ID(), $key, true);
    }
    
    public static function show_view(int $postId = 0, string $type = '')
    {
        $view = self::get_view($postId, $type);

        if ($view >= 1000) {
            $view = number_format($view / 1000, 1);
            $view .= 'K';
        }

        echo $view;
    }
    public static function update_view(int $postId = 0, string $type = 'all', int $inc = 1)
    {
        $key = $type ? "view_$type" : 'view';
        $postId = $postId ?: get_the_ID();

        if ($type === 'all') {
            update_post_meta($postId, 'view', self::get_view($postId) + $inc);
            update_post_meta($postId, 'view_day', self::get_view($postId, 'day') + $inc);
            update_post_meta($postId, 'view_week', self::get_view($postId, 'week') + $inc);
            update_post_meta($postId, 'view_month', self::get_view($postId, 'month') + $inc);
        } else {
            update_post_meta($postId, $key, self::get_view($postId, $type) + $inc);
        }
    }

    // primary category
    public static function get_primary_category(int $id = 0): \WP_Term|null
    {
        $termId = (int) get_post_meta($id ?: get_the_ID(), 'primary_category', true);
        $term = get_term($termId);

        if ($term instanceof \WP_Term) {
            return $term;
        }

        return null;
    }

    /**
     * Summary of setPrimaryCategory
     * @param int $id
     * @param int $categoryId
     * @return bool|int
     */
    public static function set_primary_category(int $id, int $categoryId)
    {
        return update_post_meta($id ?: get_the_ID(), 'primary_category', $categoryId);
    }

    public static function support_primary_category()
    {
        add_action('add_meta_boxes', function () {
            $primary_category = function () {
                $postId = get_the_ID();
                $primary = (int) get_post_meta($postId, 'primary_category', true);

                wp_dropdown_categories([
                    'name' => 'primary_category',
                    'selected' => $primary
                ]);
            };

            add_meta_box(
                'primary_category',
                'Primary Category',
                $primary_category,
                'post'
            );
        });

        add_action('save_post', function ($postId) {
            update_post_meta($postId, 'primary_category', (int) ($_POST['primary_category'] ?? 0));
        });
    }

    // like
    public static function show_like(int $id = 0)
    {
        echo (int) get_post_meta($id ?: get_the_ID(), 'like', true);
    }

    // date
    public static function get_created_ago($post = null)
    {
        return time_ago(strtotime(get_the_date('c', $post)));
    }

    public static function remove_all()
    {
        $posts = get_posts([
            'numberposts' => -1
        ]);

        if ($posts) {
            foreach ($posts as $post) {
                wp_delete_post($post->ID, true);
            }
        }
    }
}
