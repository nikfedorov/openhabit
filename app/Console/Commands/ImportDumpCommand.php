<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;

#[Description('Import a PostgreSQL dump into the current database (drops and recreates the public schema)')]
#[Signature('app:import-dump
                            {--file= : Path to the SQL dump file (reads from stdin if omitted)}')]
final class ImportDumpCommand extends Command
{
    public function handle(): int
    {
        /** @var array{host: string, port: int|string, database: string, username: string, password: string} $config */
        $config = config('database.connections.pgsql');
        $host = (string) $config['host'];
        $port = (int) $config['port'];
        $database = (string) $config['database'];
        $username = (string) $config['username'];
        $password = (string) $config['password'];

        $file = $this->option('file');
        if ($file !== null && ! file_exists($file)) {
            $this->error('File not found: '.$file);

            return 1;
        }

        $sql = (string) file_get_contents($file ?? 'php://stdin');
        if ($sql === '') {
            $this->error('No SQL content provided.');

            return 1;
        }

        // Detect the original DB owner from the dump (e.g. "OWNER TO openhabit")
        $originalOwner = null;
        if (preg_match('/\bOWNER TO (\w+)/i', $sql, $m)) {
            $originalOwner = $m[1];
        }

        // Drop and recreate the public schema via the app's DB connection
        $this->info('Dropping and recreating public schema...');
        DB::statement('DROP SCHEMA public CASCADE');
        DB::statement(sprintf('CREATE SCHEMA public AUTHORIZATION "%s"', $username));

        // Remove PostgreSQL 18 \restrict / \unrestrict sandbox metacommands —
        // they block backslash commands and are not valid in a plain psql session.
        $sql = (string) preg_replace('/^\\\\(un)?restrict\b[^\n]*\n/m', '', $sql);

        // Replace every known dump owner with the local DB username.
        // We use exact string replacement so that keywords like "stdin" are never touched.
        $dumpOwners = array_filter(array_unique([$originalOwner, 'postgres']), fn (?string $o): bool => $o !== null && $o !== $username);

        foreach ($dumpOwners as $owner) {
            $sql = str_replace('OWNER TO '.$owner, 'OWNER TO '.$username, $sql);
            $sql = str_replace('AUTHORIZATION '.$owner, 'AUTHORIZATION '.$username, $sql);
            $sql = str_replace('FOR ROLE '.$owner, 'FOR ROLE '.$username, $sql);
            $sql = str_replace(' TO '.$owner, ' TO '.$username, $sql);
            $sql = str_replace(' FROM '.$owner, ' FROM '.$username, $sql);
        }

        // Pipe the processed SQL directly into psql via stdin (-f - reads from stdin)
        $this->info('Importing dump...');

        $result = Process::input($sql)->run(sprintf(
            'PGPASSWORD=%s psql -h %s -p %d -U %s -d %s -f -',
            escapeshellarg($password),
            escapeshellarg($host),
            $port,
            escapeshellarg($username),
            escapeshellarg($database),
        ));

        if (! $result->successful()) {
            $this->error('Import failed.');

            return 1;
        }

        $this->info('Import complete.');

        return 0;
    }
}
