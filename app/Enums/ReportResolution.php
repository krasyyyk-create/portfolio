<?php

namespace App\Enums;

enum ReportResolution: string
{
    case Deleted = 'deleted';
    case Drafted = 'drafted';
    case NoAction = 'no_action';
}
