<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;

class GestionnaireMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || $request->user()->role !== 'gestionnaire') {
            return redirect()->route('produits.index')->with('error', 'Vous n\'avez pas accès à cette section.');
        }

        return $next($request);
    }
}
