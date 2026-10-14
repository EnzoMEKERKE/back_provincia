<?php

namespace App\Enum;

enum UserRole: string
{
    case ADMIN = 'ROLE_ADMIN';
    case DEBUTANT = 'ROLE_DEBUTANT';
    case MOYEN = 'ROLE_MOYEN';
    case AVANCE = 'ROLE_AVANCE';
}