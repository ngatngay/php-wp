<?php

namespace NgatNgay\WordPress;

function timeAgo(int $time)
{
    return sprintf(
        __('%s trước'),
        human_time_diff($time)
    );
}
