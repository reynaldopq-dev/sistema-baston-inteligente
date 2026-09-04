<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRol
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if(!auth()->check()){
            return redirect('/login');
        }

        if(!in_array(auth()->user()->rol, $roles, true)){
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
