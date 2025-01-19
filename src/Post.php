<?php

namespace NgatNgay\WordPress;

use NgatNgay\Http\Curl;

class Post
{
    private static string $thumbnail = '';
    
    public static function getIdBySlug($slug, $type = 'post')
    {
        $post = \get_posts([
            'name' => $slug,
            'numberposts' => 1,
            'post_type' => $type
        ]);

        return $post ? $post[0]->ID : 0;
    }

    public static function insertOrUpdate($data)
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

    public static function getRandom($limit = 10)
    {
        return get_posts([
            'posts_per_page' => $limit,
            'orderby' => 'rand'
        ]);
    }

    public static function getThumbnailUrl($post = null) {
        if (has_post_thumbnail($post)) {
            return get_the_post_thumbnail_url();
        } else {
            return self::$thumbnail;
        }
    }
    
    public static function setDefaultThumbnail(string $thumbnail) {
        self::$thumbnail = $thumbnail;
    }
    
    public static function setThumbnailUrl($post, $thumbUrl)
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
    public static function getTerms(int $post, string $taxonomy)
    {
        $res = get_the_terms($post ?: get_the_ID(), $taxonomy);
        return $res ?: [];
    }
    // view
    public static function getView(int $postId = 0, string $type = '')
    {
        $key = $type ? "view_$type" : 'view';
        return (int) get_post_meta($postId ?: get_the_ID(), $key, true);
    }
    public static function showView(int $postId = 0, string $type = '')
    {
        $view = self::getView($postId, $type);

        if ($view >= 1000) {
            $view = number_format($view / 1000, 1);
            $view .= 'K';
        }

        echo $view;
    }
    public static function updateView(int $postId = 0, string $type = 'all', int $inc = 1)
    {
        $key = $type ? "view_$type" : 'view';
        $postId = $postId ?: get_the_ID();

        if ($type === 'all') {
            update_post_meta($postId, 'view', self::getView($postId) + $inc);
            update_post_meta($postId, 'view_day', self::getView($postId, 'day') + $inc);
            update_post_meta($postId, 'view_week', self::getView($postId, 'week') + $inc);
            update_post_meta($postId, 'view_month', self::getView($postId, 'month') + $inc);
        } else {
            update_post_meta($postId, $key, self::getView($postId, $type) + $inc);
        }
    }

    // primary category
    public static function getPrimaryCategory(int $id = 0): \WP_Term|null
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
    public static function setPrimaryCategory(int $id, int $categoryId)
    {
        return update_post_meta($id ?: get_the_ID(), 'primary_category', $categoryId);
    }

    public static function supportPrimaryCategory()
    {
        add_action('add_meta_boxes', function () {
            $primaryCategory = function () {
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
                $primaryCategory,
                'post',
                //'normal',
                //'high'
            );
        });

        add_action('save_post', function ($postId) {
            update_post_meta($postId, 'primary_category', (int) ($_POST['primary_category'] ?? 0));
        });

    }

    // like
    public static function showLike($postId = 0)
    {
        $postId = $postId ?: get_the_ID();
        echo (int) get_post_meta($postId, 'like', true);
    }

    // date
    public static function showCreatedAgo($post = null)
    {
        $date = get_the_date('c', $post);
        return timeAgo(strtotime($date));
    }

    public static function removeAll()
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
