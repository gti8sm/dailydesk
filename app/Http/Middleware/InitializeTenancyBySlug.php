<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyBySlug
{
    protected array $reservedSlugs = [
        'accueil', 'login', 'logout', 'password', 'cgv', 'mentions-legales', 'rgpd',
        'central', 'api', 'up', 'onboarding',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('tenant');

        if (!$slug || in_array($slug, $this->reservedSlugs)) {
            return $next($request);
        }

        $tenant = Tenant::where('slug', $slug)->first();

        if (!$tenant) {
            abort(404, 'Organisation introuvable');
        }

        if (!in_array($tenant->status, ['active', 'prospect'])) {
            abort(403, 'Ce compte est suspendu. Contactez le support.');
        }

        tenancy()->initialize($tenant);

        URL::defaults(['tenant' => $slug]);

        $request->route()->forgetParameter('tenant');

        return $next($request);
    }
}
