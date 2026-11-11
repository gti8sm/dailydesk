<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class RgpdController extends Controller
{
    /**
     * Registre des traitements + outils RGPD (super admin).
     */
    public function index()
    {
        $this->authorizeSuperAdmin();

        $tenants = Tenant::count();

        return view('central.rgpd.index', compact('tenants'));
    }

    /**
     * Purge des présences au-delà de la durée de conservation.
     * Confirmation renforcée : saisie de « PURGER ».
     */
    public function purgePresences(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'years' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        if ($request->input('confirm') !== 'PURGER') {
            return redirect()->route('central.rgpd.index')
                ->with('error', 'Purge annulée : vous devez saisir PURGER en toutes lettres pour confirmer.');
        }

        $years = (int) $validated['years'];
        $cutoff = now()->subYears($years)->toDateString();
        $totals = ['cantine' => 0, 'garderie' => 0];

        foreach (Tenant::all() as $tenant) {
            tenancy()->initialize($tenant);

            $cantine = \App\Modules\Cantine\Models\CantinePresence::where('date', '<', $cutoff)->count();
            $garderie = \App\Modules\Garderie\Models\GarderiePresence::where('date', '<', $cutoff)->count();

            if ($cantine > 0) {
                \App\Modules\Cantine\Models\CantinePresence::where('date', '<', $cutoff)->delete();
            }
            if ($garderie > 0) {
                \App\Modules\Garderie\Models\GarderiePresence::where('date', '<', $cutoff)->delete();
            }

            $totals['cantine'] += $cantine;
            $totals['garderie'] += $garderie;
        }

        return redirect()->route('central.rgpd.index')
            ->with('success', "Purge effectuée — {$totals['cantine']} présences cantine et {$totals['garderie']} présences garderie antérieures au {$cutoff} ont été supprimées.");
    }

    private function authorizeSuperAdmin(): void
    {
        if (!auth()->user() || !auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }
    }
}
