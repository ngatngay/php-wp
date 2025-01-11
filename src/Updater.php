<?php

namespace NgatNgay\WordPress;

class Updater {
    public static function init(...$args) {
        return \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(...$args);
    }
}
