<?php

namespace wpx;

class nav
{
    public static function get($menu)
    {
        $r = function ($array, $parent = 0) use (&$r) {
            $data = [];

            foreach ($array as $value) {
                if ($value->menu_item_parent !== "$parent") {
                    continue;
                }

                $data[$value->ID] = [
                    'item' => $value,
                    'child' => $r($array, $value->ID)
                ];
            }

            return $data;
        };

        $menu = wp_get_nav_menu_items($menu);

        if ($menu !== false) {
            return $r($menu);
        } else {
            return [];
        }
    }
}
