<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Throwable;

class CreateBackup extends Command
{
    protected $signature = 'backup:create {--path= : Destination directory (defaults to storage/app/backups)}';

    protected $description = 'Create a timestamped backup of the database and managed files (never prints secrets)';

    public function handle(): int
    {
        $destination = (string) ($this->option('path') ?? storage_path('app/backups'));
        $stamp = now()->format('Ymd-His');
        $dir = rtrim($destination, '/')."/backup-{$stamp}";
        if (! @mkdir($dir, 0700, true) && ! is_dir($dir)) {
            $this->error('Cannot create backup directory.');

            return self::FAILURE;
        }
        try {
            $this->dumpDatabase($dir);
            $this->archiveFiles($dir);
            file_put_contents($dir.'/manifest.json', json_encode([
                'created_at' => now()->toDateTimeString(),
                'app' => config('app.name'),
                'commit' => trim((string) shell_exec('git rev-parse HEAD 2>/dev/null')),
                'database' => (string) config('database.default'),
            ], JSON_PRETTY_PRINT));
        } catch (Throwable $exception) {
            $this->error('Backup failed: '.$exception->getMessage());

            return self::FAILURE;
        }
        $this->info("Backup written to {$dir}.");

        return self::SUCCESS;
    }

    private function dumpDatabase(string $dir): void
    {
        $config = config('database.connections.'.config('database.default'));
        if (($config['driver'] ?? '') !== 'mysql') {
            throw new \RuntimeException('Database backups currently support the mysql driver.');
        }
        $process = new Process([
            'mysqldump',
            '--host='.$config['host'],
            '--port='.(string) $config['port'],
            '--user='.$config['username'],
            '--single-transaction',
            '--routines',
            $config['database'],
        ]);
        $process->setEnv(['MYSQL_PWD' => (string) ($config['password'] ?? '')]);
        $process->setTimeout(600);
        $process->mustRun();
        file_put_contents($dir.'/database.sql', $process->getOutput());
        $gzip = new Process(['gzip', '-f', $dir.'/database.sql']);
        $gzip->mustRun();
        $tables = DB::select('show tables');
        $this->info('Dumped '.count($tables).' tables.');
    }

    private function archiveFiles(string $dir): void
    {
        $source = storage_path('app');
        $process = new Process([
            'tar', '--exclude=./backups', '--exclude=./.gitignore', '-czf', $dir.'/files.tar.gz',
            '-C', $source, '.',
        ]);
        $process->setTimeout(600);
        $process->mustRun();
    }
}
