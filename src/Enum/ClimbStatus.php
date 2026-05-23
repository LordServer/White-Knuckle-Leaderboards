<?php

namespace App\Enum;

enum ClimbStatus: string
{
    case UNREVIEWED = 'unreviewed';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
