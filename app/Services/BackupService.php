<?php

namespace App\Services;

use Illuminate\Support\Facades\Process as ProcessFacade;
use Symfony\Component\Process\Process;

/**
 * Sauvegardes de la plateforme : base de données, uploads et code.
 * Chaque sauvegarde est un dossier horodaté dans storage/app/backups/.
 * Rotation automatique : seules les N plus récentes sont conservées.
 */
class BackupService
{
    public const KEEP = 3;

    public function backupsDir(): string
    {
        return storage_path('app/backups');
    }

    /**
     * Crée une sauvegarde complète. Retourne [succès, détails, erreurs].
     */
    public function create(): array
    {
        if (!function_exists('proc_open')) {
            return [false, [], 'proc_open est désactivé sur cet hébergement.'];
        }

        @set_time_limit(600);

        $name = now()->format('Y-m-d_His');
        $dir = $this->backupsDir() . '/' . $name;
        @mkdir($dir, 0755, true);

        $details = [];
        $errors = [];

        // 1. Base de données
        $dbFile = $dir . '/database.sql.gz';
        $dbResult = $this->dumpDatabase($dbFile);
        if ($dbResult === true) {
            $details[] = 'Base de données : ' . $this->sizeLabel(filesize($dbFile));
        } else {
            $errors[] = 'Base de données : ' . $dbResult;
        }

        // 2. Uploads (logos, photos) — storage/app/public
        $uploadsFile = $dir . '/uploads.tar.gz';
        if (is_dir(storage_path('app/public'))) {
            $this->run("tar -czf " . escapeshellarg($uploadsFile) . " -C " . escapeshellarg(storage_path('app')) . " public");
            $details[] = 'Fichiers uploadés : ' . $this->sizeLabel(filesize($uploadsFile) ?: 0);
        }

        // 3. Code + configuration (.env inclus, .git inclus pour rollback git)
        $codeFile = $dir . '/code.tar.gz';
        $this->run(
            'tar -czf ' . escapeshellarg($codeFile) . ' ' .
            '--exclude=vendor ' .
            '--exclude=node_modules ' .
            '--exclude=storage ' .
            '--exclude=public/storage ' .
            '-C ' . escapeshellarg(base_path()) . ' .'
        );
        $details[] = 'Code + configuration : ' . $this->sizeLabel(filesize($codeFile) ?: 0);

        // Rotation : ne garder que les N dernières
        $pruned = $this->prune(self::KEEP);
        if (!empty($pruned)) {
            $details[] = 'Ancienne(s) sauvegarde(s) supprimée(s) : ' . implode(', ', $pruned);
        }

        return [empty($errors), $details, $errors];
    }

    /**
     * Liste des sauvegardes : nom, date, taille totale, fichiers.
     */
    public function list(): array
    {
        $backups = [];
        $dir = $this->backupsDir();

        if (!is_dir($dir)) {
            return $backups;
        }

        foreach (array_reverse(scandir($dir)) as $entry) {
            if ($entry === '.' || $entry === '..' || !is_dir($dir . '/' . $entry)) {
                continue;
            }

            $files = [];
            $total = 0;
            foreach (scandir($dir . '/' . $entry) as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $size = filesize($dir . '/' . $entry . '/' . $file) ?: 0;
                $files[] = ['name' => $file, 'size' => $this->sizeLabel($size)];
                $total += $size;
            }

            $backups[] = [
                'name' => $entry,
                'total_size' => $this->sizeLabel($total),
                'files' => $files,
            ];
        }

        return $backups;
    }

    public function delete(string $name): bool
    {
        if (!$this->isValidName($name)) {
            return false;
        }

        $dir = $this->backupsDir() . '/' . $name;

        if (!is_dir($dir)) {
            return false;
        }

        foreach (scandir($dir) as $file) {
            if ($file !== '.' && $file !== '..') {
                @unlink($dir . '/' . $file);
            }
        }

        return @rmdir($dir);
    }

    /**
     * Chemin absolu d'un fichier de sauvegarde (sécurisé contre les traversées).
     */
    public function filePath(string $name, string $file): ?string
    {
        if (!$this->isValidName($name) || !preg_match('/^[a-z0-9_.-]+$/i', $file)) {
            return null;
        }

        $path = $this->backupsDir() . '/' . $name . '/' . $file;

        return is_file($path) ? $path : null;
    }

    /**
     * Supprime les sauvegardes au-delà des $keep plus récentes.
     */
    public function prune(int $keep = self::KEEP): array
    {
        $dir = $this->backupsDir();
        $pruned = [];

        if (!is_dir($dir)) {
            return $pruned;
        }

        $entries = array_filter(scandir($dir), fn($e) => $e !== '.' && $e !== '..' && is_dir($dir . '/' . $e));
        sort($entries); // horodatés → ordre chronologique

        while (count($entries) > $keep) {
            $oldest = array_shift($entries);
            if ($this->delete($oldest)) {
                $pruned[] = $oldest;
            }
        }

        return $pruned;
    }

    private function dumpDatabase(string $targetFile)
    {
        $connection = config('database.connections.mysql');

        if (empty($connection['database'])) {
            return 'base de données non configurée';
        }

        // Vérifier que l'utilitaire existe
        if (!$this->commandExists('mysqldump')) {
            return 'mysqldump introuvable sur cet hébergement';
        }

        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --single-transaction --quick --routines %s | gzip > %s',
            escapeshellarg($connection['host']),
            escapeshellarg($connection['port'] ?? 3306),
            escapeshellarg($connection['username']),
            escapeshellarg($connection['database']),
            escapeshellarg($targetFile)
        );

        $process = Process::fromShellCommandline($command, null, [
            'MYSQL_PWD' => $connection['password'] ?? '',
        ], null, 480);
        $process->run();

        if (!$process->isSuccessful() || !file_exists($targetFile) || filesize($targetFile) < 100) {
            return mb_substr(trim($process->getErrorOutput() ?: $process->getOutput()), 0, 300) ?: 'échec du dump';
        }

        return true;
    }

    private function commandExists(string $command): bool
    {
        $check = ProcessFacade::run('command -v ' . escapeshellarg($command) . ' >/dev/null 2>&1 && echo ok');

        return trim($check->output()) === 'ok';
    }

    private function run(string $command): array
    {
        $process = Process::fromShellCommandline($command, null, null, null, 480);
        $process->run();

        return ['success' => $process->isSuccessful(), 'output' => trim($process->getOutput() . $process->getErrorOutput())];
    }

    private function isValidName(string $name): bool
    {
        return (bool) preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}_[0-9]{6}$/', $name);
    }

    private function sizeLabel(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' Mo';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' Ko';
        }
        return $bytes . ' o';
    }
}
