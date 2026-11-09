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
     * $comment : note libre stockée avec la sauvegarde (ex: "avant migration").
     */
    public function create(?string $comment = null): array
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

        // 4. Commentaire (sauvegarde manuelle)
        $comment = trim((string) $comment);
        if ($comment !== '') {
            file_put_contents($dir . '/comment.txt', $comment);
            $details[] = 'Note : « ' . $comment . ' »';
        }

        // Rotation : ne garder que les N dernières
        $pruned = $this->prune(self::KEEP);
        if (!empty($pruned)) {
            $details[] = 'Ancienne(s) sauvegarde(s) supprimée(s) : ' . implode(', ', $pruned);
        }

        return [empty($errors), $details, $errors];
    }

    /**
     * Restaure une sauvegarde complète : code, uploads puis base de
     * données (ordre cohérent — le schéma correspond au code restauré).
     * Retourne [succès, logs].
     */
    public function restore(string $name): array
    {
        if (!$this->isValidName($name)) {
            return [false, ['Nom de sauvegarde invalide.']];
        }

        $dir = $this->backupsDir() . '/' . $name;

        if (!is_dir($dir)) {
            return [false, ["Sauvegarde {$name} introuvable."]];
        }

        @set_time_limit(900);
        $logs = [];

        // 1. Code + configuration (.env) — untar à la racine
        $codeFile = $dir . '/code.tar.gz';
        if (is_file($codeFile)) {
            $r = $this->run('tar -xzf ' . escapeshellarg($codeFile) . ' -C ' . escapeshellarg(base_path()));
            $logs[] = ['label' => 'Restauration du code et de la configuration', 'success' => $r['success'], 'output' => $r['output']];
            if (!$r['success']) {
                return [false, $logs];
            }
        }

        // 2. Fichiers uploadés — retar dans storage/app
        $uploadsFile = $dir . '/uploads.tar.gz';
        if (is_file($uploadsFile)) {
            $r = $this->run('tar -xzf ' . escapeshellarg($uploadsFile) . ' -C ' . escapeshellarg(storage_path('app')));
            $logs[] = ['label' => 'Restauration des fichiers uploadés', 'success' => $r['success'], 'output' => $r['output']];
        }

        // 3. Base de données
        $dbFile = $dir . '/database.sql.gz';
        if (is_file($dbFile)) {
            $r = $this->restoreDatabase($dbFile);
            $logs[] = ['label' => 'Restauration de la base de données', 'success' => $r === true, 'output' => $r === true ? 'Import terminé.' : $r];
            if ($r !== true) {
                return [false, $logs];
            }
        }

        return [true, $logs];
    }

    private function restoreDatabase(string $dumpFile)
    {
        $connection = config('database.connections.mysql');

        if (!$this->commandExists('mysql')) {
            return 'client mysql introuvable sur cet hébergement';
        }

        $command = sprintf(
            'zcat %s | mysql --host=%s --port=%s --user=%s %s',
            escapeshellarg($dumpFile),
            escapeshellarg($connection['host']),
            escapeshellarg($connection['port'] ?? 3306),
            escapeshellarg($connection['username']),
            escapeshellarg($connection['database'])
        );

        $process = Process::fromShellCommandline($command, null, [
            'MYSQL_PWD' => $connection['password'] ?? '',
        ], null, 600);
        $process->run();

        if (!$process->isSuccessful()) {
            return mb_substr(trim($process->getErrorOutput() ?: $process->getOutput()), 0, 300) ?: 'échec de l\'import';
        }

        return true;
    }

    public function comment(string $name): ?string
    {
        if (!$this->isValidName($name)) {
            return null;
        }

        $file = $this->backupsDir() . '/' . $name . '/comment.txt';

        return is_file($file) ? trim((string) file_get_contents($file)) : null;
    }

    public function exists(string $name): bool
    {
        return $this->isValidName($name) && is_dir($this->backupsDir() . '/' . $name);
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
                'comment' => $this->comment($entry),
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
