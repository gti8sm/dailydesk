<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Garderie\Models\GarderiePresence;
use App\Modules\Cantine\Models\CantinePresence;
use App\Models\Child;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function index()
    {
        // Vérifier que l'utilisateur a le rôle admin
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }
        
        return view('exports.index');
    }
    
    public function exportGarderie(Request $request)
    {
        // Vérifier que l'utilisateur a le rôle admin
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }
        
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:csv,excel',
        ]);
        
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        // Récupérer les présences
        $presences = GarderiePresence::with(['child.family'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('child_id')
            ->get();
        
        if ($request->format === 'csv') {
            return $this->exportGarderieCsv($presences, $startDate, $endDate);
        } else {
            return $this->exportGarderieExcel($presences, $startDate, $endDate);
        }
    }
    
    public function exportCantine(Request $request)
    {
        // Vérifier que l'utilisateur a le rôle admin
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }
        
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:csv,excel',
        ]);
        
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        // Récupérer les présences
        $presences = CantinePresence::with(['child.family'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('child_id')
            ->get();
        
        if ($request->format === 'csv') {
            return $this->exportCantineCsv($presences, $startDate, $endDate);
        } else {
            return $this->exportCantineExcel($presences, $startDate, $endDate);
        }
    }
    
    private function exportGarderieCsv($presences, $startDate, $endDate)
    {
        $filename = 'garderie_' . $startDate->format('Y-m-d') . '_' . $endDate->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($presences) {
            $file = fopen('php://output', 'w');
            
            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes
            fputcsv($file, [
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
    
    private function exportCantineCsv($presences, $startDate, $endDate)
    {
        $filename = 'cantine_' . $startDate->format('Y-m-d') . '_' . $endDate->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($presences) {
            $file = fopen('php://output', 'w');
            
            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes
            fputcsv($file, [
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
    
    private function exportGarderieExcel($presences, $startDate, $endDate)
    {
        // Pour l'instant, on utilise CSV (Excel peut l'ouvrir)
        // Plus tard, on pourra utiliser PhpSpreadsheet pour un vrai Excel
        return $this->exportGarderieCsv($presences, $startDate, $endDate);
    }
    
    private function exportCantineExcel($presences, $startDate, $endDate)
    {
        // Pour l'instant, on utilise CSV (Excel peut l'ouvrir)
        // Plus tard, on pourra utiliser PhpSpreadsheet pour un vrai Excel
        return $this->exportCantineCsv($presences, $startDate, $endDate);
    }
}
