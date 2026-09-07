<?php

namespace App\Modules\Cantine\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Modules\Cantine\Models\CantinePresence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CantinePresenceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $mealType = $request->get('meal_type', 'lunch');

        $this->autoGeneratePresences($date, $mealType);

        $presences = CantinePresence::with(['child.family', 'child.schoolClass', 'recordedBy'])
            ->forDate($date)
            ->where('meal_type', $mealType)
            ->orderBy('created_at')
            ->get();

        $presenceByChildId = $presences->keyBy('child_id');

        $children = Child::active()
            ->with(['family', 'schoolClass'])
            ->where('cantine_subscribed', true)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $groupedChildren = $children->groupBy(function ($child) {
            return $child->schoolClass?->name ?? 'Sans classe';
        });

        return view('cantine.presences.index', compact('presences', 'date', 'mealType', 'groupedChildren', 'presenceByChildId'));
    }

    private function autoGeneratePresences($date, $mealType)
    {
        $existingChildIds = CantinePresence::forDate($date)
            ->where('meal_type', $mealType)
            ->pluck('child_id')
            ->toArray();

        $subscribedChildren = Child::active()
            ->where('cantine_subscribed', true)
            ->whereNotIn('id', $existingChildIds)
            ->get();

        foreach ($subscribedChildren as $child) {
            CantinePresence::create([
                'child_id' => $child->id,
                'date' => $date,
                'meal_type' => $mealType,
                'is_present' => true,
                'recorded_by' => auth()->id(),
                'notes' => null,
            ]);
        }
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

    public function record(Request $request, Child $child)
    {
        $request->validate([
            'date' => 'required|date',
            'meal_type' => 'required|in:lunch,snack',
            'is_present' => 'boolean',
        ]);

        $presence = CantinePresence::updateOrCreate(
            [
                'child_id' => $child->id,
                'date' => $request->date,
                'meal_type' => $request->meal_type,
            ],
            [
                'is_present' => $request->get('is_present', true),
                'recorded_by' => auth()->id(),
                'notes' => $request->notes,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Présence enregistrée pour {$child->full_name}",
            'presence' => $presence,
        ]);
    }

    public function destroy(Request $request, CantinePresence $presence)
    {
        $childName = $presence->child->full_name;
        $presence->delete();

        return response()->json([
            'success' => true,
            'message' => "Présence de {$childName} annulée",
        ]);
    }

    public function update(Request $request, CantinePresence $presence)
    {
        $request->validate([
            'is_present' => 'boolean',
        ]);

        $presence->update([
            'is_present' => $request->boolean('is_present'),
            'recorded_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Présence modifiée',
        ]);
    }

    public function monthlyReport(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $report = CantinePresence::select(
                'child_id',
                'meal_type',
                DB::raw('COUNT(*) as meal_count')
            )
            ->forMonth($year, $month)
            ->where('is_present', true)
            ->groupBy('child_id', 'meal_type')
            ->with('child.family')
            ->get();

        return view('cantine.reports.monthly', compact('report', 'year', 'month'));
    }

    public function exportMonthly(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $report = CantinePresence::select(
                'child_id',
                'meal_type',
                DB::raw('COUNT(*) as meal_count')
            )
            ->forMonth($year, $month)
            ->where('is_present', true)
            ->groupBy('child_id', 'meal_type')
            ->with('child.family')
            ->get();

        $filename = "cantine_{$year}_" . str_pad($month, 2, '0', STR_PAD_LEFT) . ".csv";

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
                'Type Repas',
                'Nombre Repas'
            ], ';');
            
            // Données
            foreach ($report as $row) {
                $mealTypeLabel = $row->meal_type === 'lunch' ? 'Déjeuner' : 'Goûter';
                
                fputcsv($file, [
                    $row->child->last_name,
                    $row->child->first_name,
                    $row->child->family->family_name,
                    $mealTypeLabel,
                    $row->meal_count,
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
