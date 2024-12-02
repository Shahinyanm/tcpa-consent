<?php

namespace App\Enums;

enum ConsentStatus: string
{
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case CONSENTED = 'consented';
    case DECLINED = 'declined';
}
