<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function impersonate(Tenant $tenant)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }

        $superAdminId = auth()->id();

        // Récupérer le domaine du tenant
        $tenantDomain = $tenant->getPrimaryDomain();

        if (!$tenantDomain) {
            return back()->with('error', 'Ce tenant n\'a pas de domaine configuré.');
        }

        // Générer un token signé sécurisé
        $tokenData = [
            'tenant_id' => $tenant->id,
            'super_admin_id' => $superAdminId,
            'expires' => time() + 60, // Valide 60 secondes
            'signature' => hash_hmac('sha256', $tenant->id . $superAdminId, config('app.key')),
        ];

        $token = base64_encode(json_encode($tokenData));

        // Construire l'URL du tenant avec le token
        $port = request()->getPort();
        $portSuffix = ($port && $port != 80 && $port != 443) ? ':' . $port : '';
        $tenantUrl = request()->getScheme() . '://' . $tenantDomain . $portSuffix . '/central/do-impersonate?token=' . urlencode($token);

        return redirect($tenantUrl);
    }
    
    public function doImpersonate(Request $request)
    {
        $tokenString = $request->get('token');
        
        if (!$tokenString) {
            return redirect()->route('login')->with('error', 'Token manquant');
        }
        
        // Décoder le token
        try {
            $tokenData = json_decode(base64_decode($tokenString), true);
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Token invalide');
        }
        
        // Vérifier le token
        if (!$tokenData || !isset($tokenData['tenant_id']) || !isset($tokenData['super_admin_id']) || !isset($tokenData['expires']) || !isset($tokenData['signature'])) {
            return redirect()->route('login')->with('error', 'Token invalide');
        }
        
        // Vérifier l'expiration
        if (time() > $tokenData['expires']) {
            return redirect()->route('login')->with('error', 'Token expiré');
        }
        
        // Vérifier la signature
        $expectedSignature = hash_hmac('sha256', $tokenData['tenant_id'] . $tokenData['super_admin_id'], config('app.key'));
        if ($tokenData['signature'] !== $expectedSignature) {
            return redirect()->route('login')->with('error', 'Token invalide');
        }
        
        // La tenancy est déjà initialisée par le middleware (on arrive sur le sous-domaine du tenant)
        // Si ce n'est pas le cas, l'initialiser manuellement
        if (!tenancy()->initialized) {
            $tenant = Tenant::on('central')->find($tokenData['tenant_id']);
            if (!$tenant) {
                return redirect()->route('login')->with('error', 'Tenant introuvable');
            }
            tenancy()->initialize($tenant);
        }

        // Trouver l'admin du tenant (dans la base du tenant, qui est maintenant active)
        $tenantAdmin = \App\Models\User::whereHas('roles', function($query) {
                $query->where('name', 'admin');
            })
            ->first();

        if (!$tenantAdmin) {
            $tenantAdmin = \App\Models\User::first();
        }

        if (!$tenantAdmin) {
            return redirect()
                ->route('login')
                ->with('error', 'Aucun utilisateur trouvé pour ce tenant.');
        }

        // Se connecter en tant que cet utilisateur
        Auth::login($tenantAdmin);
        
        // Sauvegarder en session
        session(['impersonating_from' => $tokenData['super_admin_id']]);
        session(['impersonating_tenant' => tenant('id')]);

        return redirect()
            ->route('dashboard')
            ->with('success', "Vous êtes maintenant connecté en tant que {$tenantAdmin->name}");
    }

    public function stopImpersonating()
    {
        if (!session()->has('impersonating_from')) {
            return redirect()->route('dashboard');
        }

        $superAdminId = session('impersonating_from');
        
        // Déconnecter l'utilisateur actuel
        Auth::logout();
        
        // Nettoyer la session
        session()->flush();
        session()->regenerate();

        // Générer un token pour restaurer le super admin sur le domaine central
        $tokenData = [
            'super_admin_id' => $superAdminId,
            'expires' => time() + 60,
            'signature' => hash_hmac('sha256', $superAdminId, config('app.key')),
        ];

        $token = base64_encode(json_encode($tokenData));

        // Rediriger vers le domaine central avec le token
        $port = request()->getPort();
        $portSuffix = ($port && $port != 80 && $port != 443) ? ':' . $port : '';
        
        $centralDomains = config('tenancy.central_domains', []);
        
        // Si on est déjà sur un domaine central, utiliser route()
        if (in_array(request()->getHost(), $centralDomains)) {
            return redirect()->route('dashboard')
                ->with('success', 'Vous êtes de retour en tant que Super Admin');
        }
        
        // Sinon rediriger vers localhost (domaine central) avec token
        $centralDomain = $centralDomains[1] ?? 'localhost';
        $centralUrl = request()->getScheme() . '://' . $centralDomain . $portSuffix . '/central/restore-super-admin?token=' . urlencode($token);
        
        return redirect($centralUrl);
    }
    
    public function restoreSuperAdmin(Request $request)
    {
        $tokenString = $request->get('token');
        
        if (!$tokenString) {
            return redirect()->route('login')->with('error', 'Token manquant');
        }
        
        // Décoder le token
        try {
            $tokenData = json_decode(base64_decode($tokenString), true);
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Token invalide');
        }
        
        // Vérifier le token
        if (!$tokenData || !isset($tokenData['super_admin_id']) || !isset($tokenData['expires']) || !isset($tokenData['signature'])) {
            return redirect()->route('login')->with('error', 'Token invalide');
        }
        
        // Vérifier l'expiration
        if (time() > $tokenData['expires']) {
            return redirect()->route('login')->with('error', 'Token expiré');
        }
        
        // Vérifier la signature
        $expectedSignature = hash_hmac('sha256', $tokenData['super_admin_id'], config('app.key'));
        if ($tokenData['signature'] !== $expectedSignature) {
            return redirect()->route('login')->with('error', 'Token invalide');
        }
        
        // S'assurer qu'on est sur la base centrale
        if (tenancy()->initialized) {
            tenancy()->end();
        }
        
        // Reconnecter le super admin
        $superAdmin = \App\Models\User::on('central')->find($tokenData['super_admin_id']);
        
        if ($superAdmin) {
            Auth::login($superAdmin);
            
            return redirect()
                ->route('dashboard')
                ->with('success', 'Vous êtes de retour en tant que Super Admin');
        }
        
        return redirect()->route('login')->with('error', 'Super admin introuvable');
    }
}
