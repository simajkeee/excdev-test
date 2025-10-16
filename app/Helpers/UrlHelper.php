<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class UrlHelper
{
    public static function getUserUrl(Request $request): string
    {
        return $request->user()->isAdmin() ? '/dashboard' : '/';
    }
}