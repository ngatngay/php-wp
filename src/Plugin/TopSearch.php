<?php

namespace NgatNgay\WordPress\Plugin;

class TopSearch
{
    private static string $table;

    public static function init()
    {
        global $wpdb;
        self::$table = $wpdb->prefix . 'ngatngay_top_search';
    }

    public static function add(string $query)
    {
        global $wpdb;
        $table = self::$table;
        $query = substr($query, 0, 1000);

        if (empty($query)) {
            return;
        }

        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT count FROM $table WHERE query = %s",
            $query
        ));

        if ($exists !== null) {
            $wpdb->query($wpdb->prepare(
                "UPDATE $table SET count = count + 1 WHERE query = %s",
                $query
            ));
        } else {
            $wpdb->insert($table, [
                'query' => $query,
                'count' => 1
            ]);
        }

        $wpdb->query("
            DELETE FROM $table 
            WHERE count < (
                SELECT MIN(count) 
                FROM (SELECT count FROM $table ORDER BY count DESC LIMIT 100) AS temp_table
            )
        ");
    }

    public static function get(int $limit = 10)
    {
        global $wpdb;
        $table = self::$table;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table ORDER BY count DESC LIMIT %d",
                $limit
            ),
            ARRAY_A
        );
    }
}
