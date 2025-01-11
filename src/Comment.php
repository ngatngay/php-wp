<?php

namespace NgatNgay\WordPress;

class Comment
{
    public static function getByPost(int $post_id, int $page = 1, int $comments_per_page = 10, int $parent = 0)
    {
        $offset = ($page - 1) * $comments_per_page;
        $comments = get_comments([
            'post_id' => $post_id,              // ID bài viết
            'parent' => $parent,
            'number'  => $comments_per_page,   // Số bình luận mỗi trang
            'offset'  => $offset,              // Vị trí bắt đầu
            'status'  => 'approve',            // Chỉ lấy các bình luận đã được phê duyệt
            'orderby' => 'comment_date',       // Sắp xếp theo ngày
            'order'   => 'DESC',               // Sắp xếp giảm dần
        ]);

        if (!empty($comments)) {
            return $comments;
        }
        return [];
    }
}
