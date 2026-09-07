<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Family;
use App\Models\Child;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        return view('imports.index');
    }

    public function importFamilies(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        
        $data = array_map(function($line) {
            return str_getcsv($line, ';');
        }, file($path));

        // Retirer l'en-tête
        $headers = array_shift($data);

        $imported = 0;
        $errors = [];

        DB::beginTransaction();
        
        try {
            foreach ($data as $index => $row) {
                // Ignorer les lignes vides
                if (empty(array_filter($row))) {
                    continue;
                }

                $validator = Validator::make([
                    'family_name' => $row[0] ?? null,
                    'email' => $row[1] ?? null,
                    'phone' => $row[2] ?? null,
                    'address' => $row[3] ?? null,
                    'postal_code' => $row[4] ?? null,
                    'city' => $row[5] ?? null,
                ], [
                    'family_name' => 'required|string|max:255',
                    'email' => 'required|email|unique:families,email',
                    'phone' => 'nullable|string|max:20',
                    'address' => 'nullable|string|max:255',
                    'postal_code' => 'nullable|string|max:10',
                    'city' => 'nullable|string|max:100',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Ligne " . ($index + 2) . ": " . implode(', ', $validator->errors()->all());
                    continue;
                }

                Family::create([
                    'family_name' => $row[0],
                    'email' => $row[1],
                    'phone' => $row[2] ?? null,
                    'address' => $row[3] ?? null,
                    'postal_code' => $row[4] ?? null,
                    'city' => $row[5] ?? null,
                    'is_active' => true,
                ]);

                $imported++;
            }

            DB::commit();

            $message = "{$imported} famille(s) importée(s) avec succès.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " erreur(s) détectée(s).";
            }

            return redirect()->route('imports.index')
                ->with('success', $message)
                ->with('errors', $errors);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('imports.index')
                ->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }

    public function importChildren(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        
        $data = array_map(function($line) {
            return str_getcsv($line, ';');
        }, file($path));

        // Retirer l'en-tête
        $headers = array_shift($data);

        $imported = 0;
        $errors = [];

        DB::beginTransaction();
        
        try {
            foreach ($data as $index => $row) {
                // Ignorer les lignes vides
                if (empty(array_filter($row))) {
                    continue;
                }

                // Chercher la famille par email
                $family = Family::where('email', $row[4] ?? '')->first();
                
                if (!$family) {
                    $errors[] = "Ligne " . ($index + 2) . ": Famille non trouvée avec l'email " . ($row[4] ?? 'vide');
                    continue;
                }

                $validator = Validator::make([
                    'first_name' => $row[0] ?? null,
                    'last_name' => $row[1] ?? null,
                    'birth_date' => $row[2] ?? null,
                    'gender' => $row[3] ?? null,
                ], [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'birth_date' => 'required|date',
                    'gender' => 'required|in:M,F',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Ligne " . ($index + 2) . ": " . implode(', ', $validator->errors()->all());
                    continue;
                }

                Child::create([
                    'family_id' => $family->id,
                    'first_name' => $row[0],
                    'last_name' => $row[1],
                    'birth_date' => $row[2],
                    'gender' => $row[3],
                    'is_active' => true,
                ]);

                $imported++;
            }

            DB::commit();

            $message = "{$imported} enfant(s) importé(s) avec succès.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " erreur(s) détectée(s).";
            }

            return redirect()->route('imports.index')
                ->with('success', $message)
                ->with('errors', $errors);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('imports.index')
                ->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }

    public function downloadFamilyTemplate()
    {
        $filename = 'modele_familles.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes
            fputcsv($file, [
                'Nom Famille',
                'Email',
                'Téléphone',
                'Adresse',
                'Code Postal',
                'Ville'
            ], ';');
            
            // Exemple
            fputcsv($file, [
                'Dupont',
                'dupont@example.com',
                '0612345678',
                '12 rue de la Paix',
                '75001',
                'Paris'
            ], ';');
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadChildTemplate()
    {
        $filename = 'modele_enfants.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes
            fputcsv($file, [
                'Prénom',
                'Nom',
                'Date Naissance (YYYY-MM-DD)',
                'Genre (M/F)',
                'Email Famille'
            ], ';');
            
            // Exemple
            fputcsv($file, [
                'Jean',
                'Dupont',
                '2018-05-15',
                'M',
                'dupont@example.com'
            ], ';');
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
