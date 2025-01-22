<?php

namespace wpx;

class comment
{
    function get(int $post_id, int $page = 1, int $comments_per_page = 10, int $parent = 0)
    {
        $offset = ($page - 1) * $comments_per_page;
        $comments = get_comments([
            'post_id' => $post_id,
            'parent' => $parent,
            'number' => $comments_per_page,
            'offset' => $offset,
            'status' => 'approve',
            'orderby' => 'comment_date',
            'order' => 'DESC',
        ]);

        if (!empty($comments)) {
            return $comments;
        }
        return [];
    }
}
