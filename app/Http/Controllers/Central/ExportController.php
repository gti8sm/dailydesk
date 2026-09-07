<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Modules\Garderie\Models\GarderiePresence;
use App\Modules\Cantine\Models\CantinePresence;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }
        
        $tenants = Tenant::orderBy('name')->get();
        
        return view('central.exports.index', compact('tenants'));
    }
    
    public function exportGarderie(Request $request)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }
        
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:csv,excel',
        ]);
        
        $tenant = Tenant::findOrFail($request->tenant_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        // Initialiser le contexte du tenant
        tenancy()->initialize($tenant);
        
        // Récupérer les présences
        $presences = GarderiePresence::with(['child.family'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('child_id')
            ->get();
        
        // Terminer le contexte tenant
        tenancy()->end();
        
        if ($request->format === 'csv') {
            return $this->exportGarderieCsv($presences, $startDate, $endDate, $tenant);
        } else {
            return $this->exportGarderieExcel($presences, $startDate, $endDate, $tenant);
        }
    }
    
    public function exportCantine(Request $request)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }
        
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:csv,excel',
        ]);
        
        $tenant = Tenant::findOrFail($request->tenant_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        // Initialiser le contexte du tenant
        tenancy()->initialize($tenant);
        
        // Récupérer les présences
        $presences = CantinePresence::with(['child.family'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('child_id')
            ->get();
        
        // Terminer le contexte tenant
        tenancy()->end();
        
        if ($request->format === 'csv') {
            return $this->exportCantineCsv($presences, $startDate, $endDate, $tenant);
        } else {
            return $this->exportCantineExcel($presences, $startDate, $endDate, $tenant);
        }
    }
    
    private function exportGarderieCsv($presences, $startDate, $endDate, $tenant)
    {
        $filename = 'garderie_' . $tenant->slug . '_' . $startDate->format('Y-m-d') . '_' . $endDate->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($presences, $tenant) {
            $file = fopen('php://output', 'w');
            
            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes
            fputcsv($file, [
                'Tenant',
                'Date',
                'Nom Enfant',
                'Prénom Enfant',
                'Famille',
                'Heure Arrivée',
                'Heure Départ',
                'Statut',
                'Notes'
            ], ';');
            
            // Données
            foreach ($presences as $presence) {
                fputcsv($file, [
                    $tenant->name,
                    $presence->date->format('d/m/Y'),
                    $presence->child->last_name,
                    $presence->child->first_name,
                    $presence->child->family->family_name,
                    $presence->arrival_time ?? '',
                    $presence->departure_time ?? '',
                    $presence->status === 'present' ? 'Présent' : ($presence->status === 'absent' ? 'Absent' : 'Prévu'),
                    $presence->notes ?? ''
                ], ';');
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    private function exportCantineCsv($presences, $startDate, $endDate, $tenant)
    {
        $filename = 'cantine_' . $tenant->slug . '_' . $startDate->format('Y-m-d') . '_' . $endDate->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($presences, $tenant) {
            $file = fopen('php://output', 'w');
            
            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes
            fputcsv($file, [
                'Tenant',
                'Date',
                'Nom Enfant',
                'Prénom Enfant',
                'Famille',
                'Menu',
                'Statut',
                'Notes'
            ], ';');
            
            // Données
            foreach ($presences as $presence) {
                fputcsv($file, [
                    $tenant->name,
                    $presence->date->format('d/m/Y'),
                    $presence->child->last_name,
                    $presence->child->first_name,
                    $presence->child->family->family_name,
                    $presence->menu ?? '',
                    $presence->status === 'present' ? 'Présent' : ($presence->status === 'absent' ? 'Absent' : 'Prévu'),
                    $presence->notes ?? ''
                ], ';');
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    private function exportGarderieExcel($presences, $startDate, $endDate, $tenant)
    {
        return $this->exportGarderieCsv($presences, $startDate, $endDate, $tenant);
    }
    
    private function exportCantineExcel($presences, $startDate, $endDate, $tenant)
    {
        return $this->exportCantineCsv($presences, $startDate, $endDate, $tenant);
    }
}
