<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class SystemUpdateController extends Controller
{
    public function index()
    {
        $this->authorizeSuperAdmin();

        $current = $this->currentVersion();
        $updateAvailable = null;
        $remoteInfo = null;

        if ($current) {
            $updateAvailable = session('update_available');
            $remoteInfo = session('remote_info');
        }

        $backupService = app(BackupService::class);
        $backups = $backupService->list();
        $maintenanceOn = app()->isDownForMaintenance();
        $maintenanceSecret = $this->maintenanceSecret();

        return view('central.system.update', compact('current', 'updateAvailable', 'remoteInfo', 'backups', 'maintenanceOn', 'maintenanceSecret'));
    }

    public function check()
    {
        $this->authorizeSuperAdmin();

        if (!function_exists('proc_open')) {
            return redirect()->route('central.system.index')
                ->with('error', 'proc_open est désactivé sur cet hébergement — la mise à jour doit passer par le terminal.');
        }

        $fetch = $this->runCommand('git fetch origin main 2>&1');

        if (!$fetch['success']) {
            return redirect()->route('central.system.index')
                ->with('error', 'Impossible de contacter le dépôt Git : ' . mb_substr($fetch['output'], 0, 300));
        }

        $local = trim($this->runCommand('git rev-parse --short HEAD')['output']);
        $remote = trim($this->runCommand('git rev-parse --short origin/main')['output']);
        $remoteLog = trim($this->runCommand('git log origin/main -1 --format="%s"')['output']);

        $updateAvailable = $local !== $remote;

        return redirect()->route('central.system.index')
            ->with('update_available', $updateAvailable)
            ->with('remote_info', [
                'local' => $local,
                'remote' => $remote,
                'commit' => $remoteLog,
            ]);
    }

    public function update(Request $request)
    {
        $this->authorizeSuperAdmin();

        if (!function_exists('proc_open')) {
            return redirect()->route('central.system.index')
                ->with('error', 'proc_open est désactivé sur cet hébergement — la mise à jour doit passer par le terminal.');
        }

        // Sauvegarde avant mise à jour (cochée par défaut)
        $backupResult = null;

        if ($request->boolean('backup_before', true)) {
            [$ok, $details, $errors] = app(BackupService::class)->create();
            $backupResult = [
                'success' => $ok,
                'output' => ($ok ? "Sauvegarde créée.\n" : "Échec de sauvegarde.\n")
                    . implode("\n", $details)
                    . (!empty($errors) ? "\n" . implode("\n", $errors) : ''),
            ];

            // Échec de la sauvegarde base de données = on ne met pas à jour
            if (!$ok) {
                return redirect()->route('central.system.index')
                    ->with('error', 'Mise à jour annulée : la sauvegarde a échoué. ' . implode(' ', $errors));
            }
        }

        // Étapes de la mise à jour (PHP_BINARY = même binaire que le site)
        $php = PHP_BINARY;
        $steps = [
            ['Mise en maintenance', "{$php} artisan down", true],
            ['Récupération du code (git pull)', 'git pull origin main 2>&1', false],
            ['Dépendances (composer install)', 'composer install --no-dev --optimize-autoloader 2>&1', false],
            ['Migrations', "{$php} artisan migrate --force 2>&1", false],
            ['Vidage des caches', "{$php} artisan optimize:clear 2>&1", false],
            ['Cache des routes', "{$php} artisan route:cache 2>&1", false],
            ['Cache des vues', "{$php} artisan view:cache 2>&1", false],
            ['Cache de configuration', "{$php} artisan config:cache 2>&1", false],
            ['Fin de maintenance', "{$php} artisan up", true],
        ];

        @set_time_limit(600);
        $results = [];

        if ($backupResult) {
            $results[] = ['label' => 'Sauvegarde avant mise à jour', 'success' => $backupResult['success'], 'output' => $backupResult['output']];
        }
        $failed = false;

        foreach ($steps as $i => [$label, $command, $critical]) {
            $result = $this->runCommand($command);
            $results[] = ['label' => $label, 'success' => $result['success'], 'output' => $result['output']];

            // Composer peut être absent du PATH web : non bloquant si le
            // code n'a pas changé de dépendances, on l'indique clairement
            if (!$result['success'] && $i === 2 && str_contains($result['output'], 'command not found')) {
                $lastIndex = count($results) - 1;
                $results[$lastIndex]['output'] .= "\n→ Composer introuvable dans le PATH web. Si vendor/ est à jour dans le dépôt, ce n'est pas bloquant.";
                continue;
            }

            if (!$result['success'] && $critical) {
                $failed = true;
                break;
            }

            // git pull en échec = pas la peine de continuer
            if (!$result['success'] && $i === 1) {
                $failed = true;
                break;
            }
        }

        // Sécurité : s'assurer que le site repasse en ligne quoi qu'il arrive
        if ($failed) {
            $this->runCommand("{$php} artisan up");
            $results[] = ['label' => 'Fin de maintenance (après échec)', 'success' => true, 'output' => 'Remise en ligne forcée.'];
        }

        $newVersion = $this->currentVersion();

        return redirect()->route('central.system.index')
            ->with('update_results', $results)
            ->with('update_failed', $failed)
            ->with('new_version', $newVersion);
    }

    /**
     * Crée une sauvegarde complète à la demande.
     */
    public function backup()
    {
        $this->authorizeSuperAdmin();

        if (!function_exists('proc_open')) {
            return redirect()->route('central.system.index')
                ->with('error', 'proc_open est désactivé sur cet hébergement — la sauvegarde doit passer par le terminal.');
        }

        [$ok, $details, $errors] = app(BackupService::class)->create();

        if ($ok) {
            return redirect()->route('central.system.index')
                ->with('backup_success', 'Sauvegarde créée : ' . implode(' · ', $details));
        }

        return redirect()->route('central.system.index')
            ->with('error', 'Échec de la sauvegarde : ' . implode(' · ', $errors));
    }

    /**
     * Télécharge un fichier de sauvegarde (super admin uniquement).
     */
    public function downloadBackup(Request $request, string $backup, string $file)
    {
        $this->authorizeSuperAdmin();

        $path = app(BackupService::class)->filePath($backup, $file);

        if (!$path) {
            abort(404, 'Fichier de sauvegarde introuvable.');
        }

        return response()->download($path, "dailydesk_{$backup}_{$file}");
    }

    /**
     * Supprime une sauvegarde.
     */
    public function deleteBackup(Request $request, string $backup)
    {
        $this->authorizeSuperAdmin();

        if (!app(BackupService::class)->delete($backup)) {
            return redirect()->route('central.system.index')
                ->with('error', "Impossible de supprimer la sauvegarde {$backup}.");
        }

        return redirect()->route('central.system.index')
            ->with('backup_success', "Sauvegarde {$backup} supprimée.");
    }

    /**
     * Active/désactive le mode maintenance manuellement.
     * À l'activation, un secret est généré et l'admin est redirigé
     * vers son lien de contournement : il garde accès à l'interface
     * et peut désactiver la maintenance depuis la même page.
     */
    public function toggleMaintenance(Request $request)
    {
        $this->authorizeSuperAdmin();

        $php = PHP_BINARY;

        if (app()->isDownForMaintenance()) {
            $this->runCommand("{$php} artisan up");
            return redirect()->route('central.system.index')
                ->with('backup_success', 'Mode maintenance désactivé — le site est de nouveau accessible.');
        }

        $secret = bin2hex(random_bytes(8));
        $this->runCommand("{$php} artisan down --secret=" . escapeshellarg($secret));

        // Le lien /{secret} pose le cookie de contournement puis ramène
        // à la racine — l'admin retrouve l'accès immédiatement
        return redirect()->to(url('/') . '/' . $secret)
            ->with('backup_success', 'Mode maintenance activé. Ce lien admin (avec votre secret de contournement) vous donne accès.');
    }

    /**
     * Secret de contournement courant (lu depuis le fichier down),
     * pour afficher le lien admin sur la page système.
     */
    private function maintenanceSecret(): ?string
    {
        if (!app()->isDownForMaintenance()) {
            return null;
        }

        $downFile = storage_path('framework/down');

        if (!is_file($downFile)) {
            return null;
        }

        $data = json_decode((string) file_get_contents($downFile), true);

        return $data['secret'] ?? null;
    }

    private function currentVersion(): ?string
    {
        if (!function_exists('proc_open')) {
            return null;
        }

        $hash = trim($this->runCommand('git rev-parse --short HEAD')['output']);

        if (strlen($hash) < 4 || !preg_match('/^[0-9a-f]+$/', $hash)) {
            return null;
        }

        $subject = trim($this->runCommand('git log -1 --format="%s"')['output']);

        return $hash . ' — ' . mb_substr($subject, 0, 60);
    }

    private function runCommand(string $command): array
    {
        $process = Process::fromShellCommandline($command, base_path(), null, null, 480);
        $process->run();

        return [
            'success' => $process->isSuccessful(),
            'output' => trim($process->getOutput() . "\n" . $process->getErrorOutput()),
        ];
    }

    private function authorizeSuperAdmin(): void
    {
        if (!auth()->user() || !auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }
    }
}
