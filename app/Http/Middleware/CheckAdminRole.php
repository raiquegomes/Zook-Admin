<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifique se o usuário está autenticado e se tem o papel de admin
        if (auth()->check() && auth()->user()->hasRole('enterprise')) {
            return $next($request);
        }

        // Redirecione ou mostre uma mensagem de erro se o usuário não tiver o papel adequado
        return redirect('/collaborator')->with('error', 'Você não tem permissão para acessar este painel.');
    }
}
