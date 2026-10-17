<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
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

        return view('central.system.update', compact('current', 'updateAvailable', 'remoteInfo'));
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

        $dryRun = $request->boolean('dry_run');

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

        if ($dryRun) {
            $local = trim($this->runCommand('git rev-parse --short HEAD')['output']);
            $remote = trim($this->runCommand('git rev-parse --short origin/main')['output']);
            return redirect()->route('central.system.index')
                ->with('dry_run', [
                    'local' => $local,
                    'remote' => $remote,
                    'steps' => array_column($steps, 'label'),
                ]);
        }

        @set_time_limit(600);
        $results = [];
        $failed = false;

        foreach ($steps as $i => [$label, $command, $critical]) {
            $result = $this->runCommand($command);
            $results[] = ['label' => $label, 'success' => $result['success'], 'output' => $result['output']];

            // Composer peut être absent du PATH web : non bloquant si le
            // code n'a pas changé de dépendances, on l'indique clairement
            if (!$result['success'] && $i === 2 && str_contains($result['output'], 'command not found')) {
                $results[2]['output'] .= "\n→ Composer introuvable dans le PATH web. Si vendor/ est à jour dans le dépôt, ce n'est pas bloquant.";
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
