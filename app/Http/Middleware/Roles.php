<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Roles
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(in_array(auth()->user()->role, $roles)){
            return $next($request);
        }
        
        abort('403', "Kamu tidak memiliki izin mengakses halaman ini");
    }
}
