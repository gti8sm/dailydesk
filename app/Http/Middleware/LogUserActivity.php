<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check() && $request->isMethod('POST')) {
            $route = $request->route();
            $routeName = $route?->getName() ?? '';
            $path = $request->path();

            if (str_contains($routeName, 'login') || str_contains($path, 'login')) {
                if (Auth::check()) {
                    ActivityLog::create([
                        'tenant_id' => Auth::user()->tenant_id,
                        'user_id' => Auth::user()->id,
                        'user_name' => Auth::user()->name,
                        'action' => 'login',
                        'model_type' => 'App\Models\User',
                        'model_id' => (string) Auth::user()->id,
                        'description' => 'Connexion de ' . Auth::user()->email,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                }
            } elseif (str_contains($routeName, 'logout') || str_contains($path, 'logout')) {
                ActivityLog::create([
                    'tenant_id' => Auth::user()->tenant_id ?? null,
                    'user_id' => Auth::user()->id,
                    'user_name' => Auth::user()->name,
                    'action' => 'logout',
                    'model_type' => 'App\Models\User',
                    'model_id' => (string) Auth::user()->id,
                    'description' => 'Déconnexion de ' . Auth::user()->email,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }

        return $response;
    }
}
