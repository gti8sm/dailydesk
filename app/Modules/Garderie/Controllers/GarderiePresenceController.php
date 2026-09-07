<?php

namespace App\Modules\Garderie\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Modules\Garderie\Models\GarderiePresence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GarderiePresenceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));

        $children = Child::active()
            ->with(['family', 'schoolClass'])
            ->where('garderie_subscribed', true)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(function ($child) use ($date) {
                $presence = GarderiePresence::where('child_id', $child->id)
                    ->whereDate('date', $date)
                    ->with(['recordedByArrival', 'recordedByDeparture'])
                    ->first();

                $child->presence = $presence;
                return $child;
            });

        $groupedChildren = $children->groupBy(function ($child) {
            return $child->schoolClass?->name ?? 'Sans classe';
        });

        return view('garderie.presences.index', compact('groupedChildren', 'date'));
    }

    public function search(Request $request)
    {
        $search = $request->get('q', '');
        
        $query = Child::active()->with('family');
        
        if (!empty($search)) {
            $query->search($search)->limit(10);
        }
        
        $children = $query->get();

        return response()->json($children);
    }

    public function recordArrival(Request $request, Child $child)
    {
        $request->validate([
            'date' => 'required|date',
            'arrival_time' => 'required|date_format:H:i',
        ]);

        $presence = GarderiePresence::updateOrCreate(
            [
                'child_id' => $child->id,
                'date' => $request->date,
            ],
            [
                'arrival_time' => $request->arrival_time,
                'recorded_by_arrival' => auth()->id(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Arrivée enregistrée pour {$child->full_name}",
            'presence' => $presence,
        ]);
    }

    public function recordDeparture(Request $request, Child $child)
    {
        $request->validate([
            'date' => 'required|date',
            'departure_time' => 'required|date_format:H:i',
        ]);

        // Chercher ou créer la présence pour ce jour
        $presence = GarderiePresence::where('child_id', $child->id)
            ->whereDate('date', $request->date)
            ->first();
        
        if (!$presence) {
            $presence = GarderiePresence::create([
                'child_id' => $child->id,
                'date' => $request->date,
                'arrival_time' => null,
                'recorded_by_arrival' => null,
            ]);
        }

        $presence->update([
            'departure_time' => $request->departure_time,
            'recorded_by_departure' => auth()->id(),
        ]);

        $presence->calculateDuration();

        return response()->json([
            'success' => true,
            'message' => "Départ enregistré pour {$child->full_name}",
            'presence' => $presence,
        ]);
    }

    public function destroy(Request $request, GarderiePresence $presence)
    {
        $childName = $presence->child->full_name;
        $presence->delete();

        return response()->json([
            'success' => true,
            'message' => "Présence supprimée pour {$childName}",
        ]);
    }

    public function update(Request $request, GarderiePresence $presence)
    {
        $request->validate([
            'arrival_time' => 'nullable|date_format:H:i',
            'departure_time' => 'nullable|date_format:H:i',
        ]);

        if ($request->has('arrival_time')) {
            $presence->arrival_time = $request->arrival_time;
        }
        if ($request->has('departure_time')) {
            $presence->departure_time = $request->departure_time;
        }
        $presence->save();
        $presence->calculateDuration();

        return response()->json([
            'success' => true,
            'message' => 'Présence modifiée',
            'presence' => $presence,
        ]);
    }

    public function monthlyReport(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $report = GarderiePresence::select('child_id', DB::raw('COUNT(*) as days_count'), DB::raw('SUM(duration_minutes) as total_minutes'))
            ->forMonth($year, $month)
            ->whereNotNull('arrival_time')
            ->whereNotNull('departure_time')
            ->groupBy('child_id')
            ->with('child.family')
            ->get();

        return view('garderie.reports.monthly', compact('report', 'year', 'month'));
    }

    public function exportMonthly(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $report = GarderiePresence::select('child_id', DB::raw('COUNT(*) as days_count'), DB::raw('SUM(duration_minutes) as total_minutes'))
            ->forMonth($year, $month)
            ->whereNotNull('arrival_time')
            ->whereNotNull('departure_time')
            ->groupBy('child_id')
            ->with('child.family')
            ->get();

        $filename = "garderie_{$year}_" . str_pad($month, 2, '0', STR_PAD_LEFT) . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($report) {
            $file = fopen('php://output', 'w');
            
            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes
            fputcsv($file, [
                'Nom Enfant',
                'Prénom Enfant',
                'Famille',
                'Jours Présents',
                'Total Minutes',
                'Total Heures',
                'Moyenne Heures/Jour'
            ], ';');
            
            // Données
            foreach ($report as $row) {
                $totalHours = $row->total_minutes / 60;
                $avgHours = $row->days_count > 0 ? $totalHours / $row->days_count : 0;
                
                fputcsv($file, [
                    $row->child->last_name,
                    $row->child->first_name,
                    $row->child->family->family_name,
                    $row->days_count,
                    $row->total_minutes,
                    number_format($totalHours, 2, '.', ''),
                    number_format($avgHours, 2, '.', ''),
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
