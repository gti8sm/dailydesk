<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Réserve l'administration centrale (préfixe /central) au super admin.
 * Un utilisateur de mairie qui tente d'y accéder est redirigé
 * proprement vers le dashboard de sa commune (et non un 500 :
 * les vues centrales supposent un contexte sans tenant).
 */
class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Routes publiques du groupe (restauration d'impersonation par
        // token signé) : pas d'utilisateur connecté, le contrôleur
        // valide le token lui-même
        if (!$user) {
            return $next($request);
        }

        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        $tenant = Tenant::find($user->tenant_id);

        if ($tenant) {
            return redirect()->route('dashboard', ['tenant' => $tenant->slug])
                ->with('error', 'Cette section est réservée à l\'administration de la plateforme.');
        }

        abort(403, 'Accès non autorisé');
    }
}
