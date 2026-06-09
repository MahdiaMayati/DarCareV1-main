<?php
// app/Enums/RequestStatusEnum.php

namespace App\Enums;

enum RequestStatusEnum: string
{
    case Pending  = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Delayed  = 'delayed';
    case Completed = 'completed';
}
