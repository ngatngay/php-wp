<?php

namespace wpx;

class tax
{
    public static function add($key, $title, $type = 'post', $args = [])
    {
        $args = array_merge([
            'labels' => [
                'name' => $title
            ]
        ], $args);
        register_taxonomy($key, $type, $args);
    }

    public static function get_terms(array $opt)
    {
        $offset = ($opt['page'] - 1) * $opt['per_page'];
        $opt += [
            //'taxonomy'   => $taxonomy,    // Tên taxonomy
            'hide_empty' => false,        // Bao gồm các term không có bài viết
            'number' => $opt['per_page'],    // Số term mỗi trang
            'offset' => $offset,      // Bắt đầu từ vị trí nào
        ];
        $terms = get_terms($opt);
        return !empty($terms) ? $terms : [];
    }

    public static function count_post(string $taxonomy)
    {
        $query = new \WP_Query([
            'post_type' => 'post',
            'tax_query' => [
                [
                    'taxonomy' => $taxonomy,
                    'operator' => 'EXISTS',
                ],
            ],
        ]);

        return (int) $query->found_posts;
    }

    public static function count_post_by_term(string $taxonomy, string $term)
    {
        $query = new \WP_Query([
            'post_type' => 'post',
            'tax_query' => [
                [
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $term
                ],
            ],
        ]);

        return (int) $query->found_posts;
    }
    
    public static function show_dropdown_categories($args) {
        $query = get_option( 'category_base' );
        
        if (empty($args['id'])) {
            throw new \Exception('miss id');
        }
        if (!empty($args['taxonomy'])) {
if  ($args['taxonomy'] == 'post_tag') {
            $query = get_option( 'tag_base' );
            } else {
                $query = $args['taxonomy'];
            }
        }
        
        wp_dropdown_categories($args);

        echo  '<script>
        document.getElementById("' . $args['id'] . '").addEventListener("change", function() {
            let id = this.value;
            if (id) {
                const url = "' . esc_url(home_url('/')) . $query . '/" + id;
                window.location.href = url;
            }
        });
        </script>';
    }
}
