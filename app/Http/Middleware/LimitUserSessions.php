<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LimitUserSessions
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $maxAllowed = (int) ($user->max_sessions ?? 1);

        // Ventana de vida real (ej: 2 minutos)
        $activeWindow = now()->subMinutes(2)->timestamp;

        // Limpia sesiones muertas
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('last_activity', '<', $activeWindow)
            ->delete();

        // Cuenta sesiones realmente activas
        $activeSessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('last_activity', '>=', $activeWindow)
            ->count();

        if ($activeSessions > $maxAllowed) {

            Auth::logout();

            return redirect()
                ->route('login')
                ->withErrors([
                    'session_limit' =>
                        'Ya alcanzaste el número máximo de sesiones activas.'
                ]);
        }

        return $next($request);
    }

}
