<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */

    /*
    * Lic. Rodrigo Aguilar García - 2024
    */

    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('welcome');
    }
}
