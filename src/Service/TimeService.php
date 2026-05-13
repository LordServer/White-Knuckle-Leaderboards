<?php

namespace App\Service;

class TimeService
{
    public function timeAgo($dateTime): string
    {
        $secondsAgo = strtotime('now') - strtotime($dateTime->format('Y-m-d H:i:s'));

        $labels = ['year', 'month', 'week', 'day', 'hour', 'minute', 'second'];
        $values = [
            floor($secondsAgo / 31556736),
            floor(($secondsAgo % 31556736) / 2629728), // months
            floor(($secondsAgo % 2629728) / 604800), // weeks
            floor(($secondsAgo % 604800) / 86400), // days
            floor(($secondsAgo % 86400) / 3600), // hours
            floor(($secondsAgo % 3600) / 60), // minutes
            floor($secondsAgo % 60), // seconds
        ];

        $pairs = [];
        for ($i = 0; $i < count($labels); ++$i) {
            $str = $values[$i] > 0 ? $values[$i].' '.$labels[$i] : '';
            $str .= $values[$i] > 1 ? 's' : '';
            if ('' != $str) {
                $pairs[] = $str;
            }
        }

        return $pairs[0].' ago';
    }
}
