<?php

/*
 * Plugin Name: WordPress Helper
 * Version: 1.6
 */

defined('ABSPATH') or exit;

use NgatNgay\WordPress\Updater;
use NgatNgay\WordPress\Cron;

require __DIR__ . '/vendor/autoload.php';

// require
if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
    wp_die('cron are disable');
}

Cron::add('ngatngay', function () {
    file_get_contents('https://xn--ngc-bmz.vn/api/monitor?key=ngatngay&name=' . rawurldecode(get_site_url()) . '&type=wordpress');
}, ['repeat' => 'hourly']);

// Kích hoạt plugin và tạo bảng
register_activation_hook(__FILE__, function () {
    global $wpdb;

    $table_name = $wpdb->prefix . 'ngatngay_top_search';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    query VARCHAR(4096) NOT NULL,
    count INT NOT NULL DEFAULT 0,
    FULLTEXT (query)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});

// update
Updater::init(
    'https://cdn.ngatngay.net/wp/ngatngay/plugin.json',
    __FILE__
);
