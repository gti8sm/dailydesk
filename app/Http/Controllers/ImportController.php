<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Family;
use App\Models\ParentModel;
use App\Models\Child;
use App\Models\School;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

        $data = $this->readCsv($request);

        $imported = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($data as $index => $row) {
                $line = $index + 2;

                if ($this->isEmptyRow($row)) {
                    continue;
                }

                $validator = Validator::make([
                    'family_name' => $row[0] ?? null,
                    'email' => $row[1] ?? null,
                    'phone' => $row[2] ?? null,
                    'address' => $row[3] ?? null,
                    'postal_code' => $row[4] ?? null,
                    'city' => $row[5] ?? null,
                    'parent_first_name' => $row[6] ?? null,
                    'parent_last_name' => $row[7] ?? null,
                    'parent_mobile' => $row[8] ?? null,
                    'relationship' => $row[9] ?? null,
                ], [
                    'family_name' => 'required|string|max:255',
                    // Unicité par tenant uniquement, et les familles supprimées
                    // (soft delete) ne bloquent pas un ré-import
                    'email' => [
                        'required',
                        'email',
                        Rule::unique('families', 'email')
                            ->where(fn($q) => $q
                                ->where('tenant_id', tenant()->id)
                                ->whereNull('deleted_at')),
                    ],
                    'phone' => 'nullable|string|max:20',
                    'address' => 'nullable|string|max:255',
                    'postal_code' => 'nullable|string|max:10',
                    'city' => 'nullable|string|max:100',
                    'parent_first_name' => 'nullable|string|max:255',
                    'parent_last_name' => 'nullable|string|max:255',
                    'parent_mobile' => 'nullable|string|max:20',
                    'relationship' => 'nullable|string|max:255',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Ligne {$line} : " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $family = Family::create([
                    'family_name' => $row[0],
                    'email' => $row[1],
                    'phone' => $row[2] ?? null,
                    'address' => $row[3] ?? null,
                    'postal_code' => $row[4] ?? null,
                    'city' => $row[5] ?? null,
                    'is_active' => true,
                ]);

                // Créer la fiche parent (premier contact) si prénom ou nom renseigné
                $parentFirstName = trim($row[6] ?? '');
                $parentLastName = trim($row[7] ?? '');

                if ($parentFirstName !== '' || $parentLastName !== '') {
                    ParentModel::create([
                        'family_id' => $family->id,
                        'first_name' => $parentFirstName ?: $family->family_name,
                        'last_name' => $parentLastName ?: $family->family_name,
                        'email' => $family->email,
                        'phone' => $family->phone,
                        'mobile' => $row[8] ?? null,
                        'address' => $family->address,
                        'postal_code' => $family->postal_code,
                        'city' => $family->city,
                        'relationship' => $this->parseRelationship($row[9] ?? null),
                        'is_primary_contact' => true,
                        'can_pickup' => true,
                        'is_legal_guardian' => true,
                    ]);
                }

                $imported++;
            }

            DB::commit();

            $message = "{$imported} famille(s) importée(s) avec succès.";
            if (!empty($errors)) {
                $message .= ' ' . count($errors) . ' erreur(s) détectée(s).';
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

        $data = $this->readCsv($request);

        // Cache des écoles/classes pour le matching par nom
        $schools = School::active()->get();
        $classes = SchoolClass::active()->get();

        $imported = 0;
        $errors = [];
        $skipped = 0;

        DB::beginTransaction();

        try {
            foreach ($data as $index => $row) {
                $line = $index + 2;

                if ($this->isEmptyRow($row)) {
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
                    $errors[] = "Ligne {$line} : " . implode(', ', $validator->errors()->all());
                    continue;
                }

                // Rattachement à la famille par email
                $family = Family::where('email', trim($row[4] ?? ''))->first();

                if (!$family) {
                    $errors[] = "Ligne {$line} : famille non trouvée avec l'email « " . trim($row[4] ?? '') . ' »';
                    continue;
                }

                // Anti double-import : même famille + prénom + nom + naissance
                $alreadyExists = Child::where('family_id', $family->id)
                    ->where('first_name', trim($row[0]))
                    ->where('last_name', trim($row[1]))
                    ->whereDate('birth_date', $row[2])
                    ->exists();

                if ($alreadyExists) {
                    $skipped++;
                    continue;
                }

                // École par nom (insensible à la casse et aux accents)
                $school = null;
                $schoolName = trim($row[5] ?? '');

                if ($schoolName !== '') {
                    $school = $schools->first(fn($s) => $this->normalizeName($s->name) === $this->normalizeName($schoolName));

                    if (!$school) {
                        $available = $schools->pluck('name')->implode(' », «');
                        $errors[] = "Ligne {$line} : école « {$schoolName} » introuvable. Écoles disponibles : « {$available} »";
                        continue;
                    }
                }

                // Classe par nom, dans l'école si précisée
                $class = null;
                $className = trim($row[6] ?? '');

                if ($className !== '') {
                    $candidates = $classes
                        ->when($school, fn($c) => $c->where('school_id', $school->id));

                    $class = $candidates->first(fn($c) => $this->normalizeName($c->name) === $this->normalizeName($className));

                    if (!$class && $school) {
                        $available = $classes->where('school_id', $school->id)->pluck('name')->implode(' », «');
                        $errors[] = "Ligne {$line} : classe « {$className} » introuvable dans « {$school->name} ». Classes disponibles : « {$available} »";
                        continue;
                    }

                    if (!$class) {
                        // Sans école précisée : la classe doit être unique
                        $matches = $classes->filter(fn($c) => $this->normalizeName($c->name) === $this->normalizeName($className));

                        if ($matches->count() > 1) {
                            $errors[] = "Ligne {$line} : classe « {$className} » ambiguë (présente dans plusieurs écoles). Précisez l'école.";
                            continue;
                        }
                    }
                }

                Child::create([
                    'family_id' => $family->id,
                    'school_id' => $school?->id,
                    'class_id' => $class?->id,
                    'first_name' => $row[0],
                    'last_name' => $row[1],
                    'birth_date' => $row[2],
                    'gender' => $row[3],
                    'allergies' => trim($row[9] ?? '') ?: null,
                    'cantine_subscribed' => $this->parseBoolean($row[7] ?? null),
                    'garderie_subscribed' => $this->parseBoolean($row[8] ?? null),
                    'is_active' => true,
                ]);

                $imported++;
            }

            DB::commit();

            $message = "{$imported} enfant(s) importé(s) avec succès.";
            if ($skipped > 0) {
                $message .= " {$skipped} déjà existant(s) ignoré(s) (ré-import détecté).";
            }
            if (!empty($errors)) {
                $message .= ' ' . count($errors) . ' erreur(s) détectée(s).';
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

            fputcsv($file, [
                'Nom Famille',
                'Email',
                'Téléphone',
                'Adresse',
                'Code Postal',
                'Ville',
                'Prénom Parent',
                'Nom Parent',
                'Mobile Parent',
                'Relation (mere/pere/tuteur)'
            ], ';');

            fputcsv($file, [
                'Dupont',
                'dupont@example.com',
                '0612345678',
                '12 rue de la Paix',
                '75001',
                'Paris',
                'Nathalie',
                'Dupont',
                '0698765432',
                'mere'
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

            fputcsv($file, [
                'Prénom',
                'Nom',
                'Date Naissance (YYYY-MM-DD)',
                'Genre (M/F)',
                'Email Famille',
                'École',
                'Classe',
                'Cantine (oui/non)',
                'Garderie (oui/non)',
                'Allergies'
            ], ';');

            fputcsv($file, [
                'Jean',
                'Dupont',
                '2018-05-15',
                'M',
                'dupont@example.com',
                'École Élémentaire Jean Moulin',
                'CP',
                'oui',
                'non',
                ''
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Lit le CSV uploadé : séparateur «;», en-tête retiré.
     */
    private function readCsv(Request $request): array
    {
        $file = $request->file('file');
        $lines = file($file->getRealPath());

        $data = array_map(fn($line) => str_getcsv($line, ';', '"', '\\'), $lines);
        array_shift($data); // en-tête

        return $data;
    }

    private function isEmptyRow(array $row): bool
    {
        return empty(array_filter($row, fn($value) => trim((string) $value) !== ''));
    }

    /**
     * Normalise un nom pour le matching : minuscules sans accents ni espaces superflus.
     */
    private function normalizeName(string $name): string
    {
        $normalized = mb_strtolower(trim($name));
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $normalized) ?: $normalized;
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        return trim($normalized);
    }

    /**
     * Interprète un booléen CSV : oui/non, O/N, 1/0, true/false, x.
     */
    private function parseBoolean(?string $value): bool
    {
        $value = mb_strtolower(trim((string) $value));

        return in_array($value, ['oui', 'o', '1', 'true', 'vrai', 'x', 'yes', 'y']);
    }

    /**
     * Interprète la relation du parent : mère/père/tuteur → enum parents.
     */
    private function parseRelationship(?string $value): string
    {
        $value = mb_strtolower(trim((string) $value));

        if (in_array($value, ['mere', 'mère', 'maman', 'mother', 'm'])) {
            return 'mother';
        }

        if (in_array($value, ['pere', 'père', 'papa', 'father', 'p'])) {
            return 'father';
        }

        if (in_array($value, ['tuteur', 'tutrice', 'guardian', 't'])) {
            return 'guardian';
        }

        return 'other';
    }
}
