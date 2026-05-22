<?php

namespace App\Util;

class TimeFormatter
{
    public static function secondsToTime(float $seconds): string
    {
        $msec = ($seconds * 100) % 100;
        $secs = $seconds % 60;
        $mins = floor($seconds / 60) % 60;
        $hours = floor($seconds / 3600);

        if (0 != $hours) {
            return sprintf('%02d:%02d:%02d.%02d', $hours, $mins, $secs, $msec);
        } elseif (0 != $mins) {
            return sprintf('%02d:%02d.%02d', $mins, $secs, $msec);
        }

        return sprintf('%02d.%02d', $secs, $msec);
    }
}
