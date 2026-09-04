<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForzarCambioPassword
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->debe_cambiar_password) {
            return redirect()->route('password.cambiar');
        }

        return $next($request);
    }
}
