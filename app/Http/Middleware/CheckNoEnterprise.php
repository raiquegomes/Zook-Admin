<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Middleware\CheckNoEnterprise;

class CheckNoEnterprise
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Verifique se o usuário tem algum enterprise vinculado
        if ($user && $user->enterprises()->exists()) {
            // Redirecionar para uma página de erro ou qualquer outra página
            return redirect('/collaborator')->with('error', 'Você já tem um enterprise vinculado.');
        }

        return $next($request);
    }
}
