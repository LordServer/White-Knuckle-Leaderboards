<?php

namespace App\Enum;

enum Status: string
{
    case UNREVIEWED = 'unreviewed';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
