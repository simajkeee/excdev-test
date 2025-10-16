<?php

namespace App\Enums;

enum Roles: string
{
    case ADMIN = 'admin';

    case ACCOUNT_OWNER = 'account_owner';
}