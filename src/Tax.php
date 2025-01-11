<?php

namespace NgatNgay\WordPress;

class Tax
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
    
    public static function getTerms(array $opt) {
    $offset = ($opt['page'] - 1) * $opt['per_page'];
    $opt += [
        //'taxonomy'   => $taxonomy,    // Tên taxonomy
        'hide_empty' => false,        // Bao gồm các term không có bài viết
        'number'     => $opt['per_page'],    // Số term mỗi trang
        'offset'     => $offset,      // Bắt đầu từ vị trí nào
    ];
    $terms = get_terms($opt);
    return !empty($terms) ? $terms : [];
    }
}
