<?php

namespace wpx;

class updater {
    public static function init(...$args) {
        return \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(...$args);
    }
}
