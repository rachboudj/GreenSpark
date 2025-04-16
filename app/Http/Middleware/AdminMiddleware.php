<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est connecté et est un administrateur
        if (!$request->user() || $request->user()->role !== 'Admin') {
            return redirect()->route('home')->with('error', 'Accès non autorisé. Vous devez être administrateur.');
        }

        return $next($request);
    }
}