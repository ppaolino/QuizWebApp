<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isCreator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ((!auth()->check()) || (auth()->user()->role != 'creator')) {
            return response()->view('errors.accessDenied', ['message' => 'Only creators can access this page!'], 403);
        }
        return $next($request);
    }
}
