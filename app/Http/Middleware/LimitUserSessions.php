<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LimitUserSessions
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {

            $user = Auth::user();

            // Valor máximo permitido (directo de la columna users.max_sessions)
            $maxAllowed = $user->max_sessions ?? 1; // por si acaso

            // Contar sesiones activas
            $activeSessions = DB::table('sessions')
                ->where('user_id', $user->id)
                ->count();

            // Si excede el límite → cerrar sesión
            if ($activeSessions > $maxAllowed) {

                Auth::logout();

                return redirect()->route('login')
                    ->withErrors([
                        'session_limit' => 'Has alcanzado el número máximo de sesiones permitidas.'
                    ]);
            }
        }

        return $next($request);
    }

}
