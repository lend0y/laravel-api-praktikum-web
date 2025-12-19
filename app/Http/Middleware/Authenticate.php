<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Handle unauthenticated requests for API (JWT).
     */
    protected function redirectTo(Request $request): ?string
    {
        return null;
    }
}
