<?php

namespace App\Util;

use App\Entity\Climb;

class ClimbName
{
    public static function pageName(Climb $climb): string
    {
        if ('Normal' === $climb->getSubcategory()->getName()) {
            $pageName = $climb->getCategory()->getName();
        } else {
            $pageName = $climb->getSubcategory()->getName().' '.$climb->getCategory()->getName();
        }

        $pageName = $pageName.' <span class="font-normal text-gray-700 text-sm">with a ';

        $rankMethod = $climb->getCategory()->getRankMethod()->getName();
        if (str_contains($rankMethod, 'Score')) {
            $pageName = $pageName.'score of</span> '.number_format($climb->getScore());
        } elseif (str_contains($rankMethod, 'Time')) {
            $pageName = $pageName.'time of</span> '.TimeFormatter::secondsToTime($climb->getTime());
        } elseif (str_contains($rankMethod, 'Height')) {
            $pageName = $pageName.'height of</span> '.number_format($climb->getHeight(), 2).' m';
        } elseif (str_contains($rankMethod, 'Speed')) {
            $pageName = $pageName.'speed of</span> '.number_format($climb->getSpeed(), 2).' m/s';
        }

        return $pageName.' <span class="font-normal text-gray-700 text-sm">by</span> '.$climb->getClimber()->getDisplayName();
    }
}
