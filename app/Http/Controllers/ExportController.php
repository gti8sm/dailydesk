<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Garderie\Models\GarderiePresence;
use App\Modules\Cantine\Models\CantinePresence;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        return view('exports.index');
    }

    public function exportGarderie(Request $request)
    {
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

        // Rapport agrégé par enfant : jours, heures de présence réelles
        $report = GarderiePresence::select(
                'child_id',
                DB::raw('COUNT(*) as total_days'),
                DB::raw('SUM(CASE WHEN arrival_time IS NOT NULL AND departure_time IS NOT NULL THEN 1 ELSE 0 END) as days_count'),
                DB::raw('COALESCE(SUM(duration_minutes), 0) as total_minutes')
            )
            ->whereBetween('date', [$startDate, $endDate])
            ->where(function ($q) {
                $q->whereNotNull('arrival_time')->orWhereNotNull('departure_time');
            })
            ->groupBy('child_id')
            ->with('child.family')
            ->get();

        return $this->exportGarderieCsv($report, $startDate, $endDate);
    }

    public function exportCantine(Request $request)
    {
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

        // Rapport agrégé par enfant : nombre de repas pris par type
        $report = CantinePresence::select(
                'child_id',
                'meal_type',
                DB::raw('COUNT(*) as meal_count')
            )
            ->whereBetween('date', [$startDate, $endDate])
            ->where('is_present', true)
            ->groupBy('child_id', 'meal_type')
            ->with('child.family')
            ->get();

        return $this->exportCantineCsv($report, $startDate, $endDate);
    }

    private function exportGarderieCsv($report, $startDate, $endDate)
    {
        $filename = 'garderie_' . $startDate->format('Y-m-d') . '_' . $endDate->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($report, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['Rapport garderie du ' . $startDate->format('d/m/Y') . ' au ' . $endDate->format('d/m/Y')], ';');
            fputcsv($file, [], ';');

            fputcsv($file, [
                'Nom Enfant',
                'Prénom Enfant',
                'Famille',
                'Jours avec présence',
                'Jours complets (arrivée + départ)',
                'Total Minutes',
                'Total Heures',
                'Moyenne Heures/Jour complet'
            ], ';');

            foreach ($report as $row) {
                $totalHours = $row->total_minutes / 60;
                $avgHours = $row->days_count > 0 ? $totalHours / $row->days_count : 0;

                fputcsv($file, [
                    $row->child->last_name,
                    $row->child->first_name,
                    $row->child->family->family_name,
                    $row->total_days,
                    $row->days_count,
                    $row->total_minutes,
                    number_format($totalHours, 2, '.', ''),
                    number_format($avgHours, 2, '.', ''),
                ], ';');
            }

            // Totaux généraux
            $totalMinutes = $report->sum('total_minutes');
            fputcsv($file, [], ';');
            fputcsv($file, [
                'TOTAUX',
                '',
                '',
                $report->sum('total_days'),
                $report->sum('days_count'),
                $totalMinutes,
                number_format($totalMinutes / 60, 2, '.', ''),
                '',
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportCantineCsv($report, $startDate, $endDate)
    {
        $filename = 'cantine_' . $startDate->format('Y-m-d') . '_' . $endDate->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($report, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['Rapport cantine du ' . $startDate->format('d/m/Y') . ' au ' . $endDate->format('d/m/Y')], ';');
            fputcsv($file, [], ';');

            fputcsv($file, [
                'Nom Enfant',
                'Prénom Enfant',
                'Famille',
                'Type Repas',
                'Nombre Repas'
            ], ';');

            foreach ($report as $row) {
                fputcsv($file, [
                    $row->child->last_name,
                    $row->child->first_name,
                    $row->child->family->family_name,
                    $row->meal_type === 'lunch' ? 'Déjeuner' : 'Goûter',
                    $row->meal_count,
                ], ';');
            }

            // Totaux généraux
            fputcsv($file, [], ';');
            fputcsv($file, [
                'TOTAUX',
                '',
                '',
                'Déjeuners: ' . $report->where('meal_type', 'lunch')->sum('meal_count')
                    . ' — Goûters: ' . $report->where('meal_type', 'snack')->sum('meal_count'),
                $report->sum('meal_count'),
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
