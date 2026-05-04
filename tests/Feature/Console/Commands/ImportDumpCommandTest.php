<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;

it('fails when file does not exist', function (): void {
    $this->artisan('app:import-dump', ['--file' => '/nonexistent/dump.sql'])
        ->expectsOutputToContain('File not found')
        ->assertExitCode(1);
});

it('fails when no sql content provided', function (): void {
    $tmpFile = (string) tempnam(sys_get_temp_dir(), 'test_');
    file_put_contents($tmpFile, '');

    $this->artisan('app:import-dump', ['--file' => $tmpFile])
        ->expectsOutputToContain('No SQL content provided')
        ->assertExitCode(1);

    unlink($tmpFile);
});

it('drops schema, replaces owners and runs psql successfully', function (): void {
    $tmpFile = (string) tempnam(sys_get_temp_dir(), 'test_');
    file_put_contents($tmpFile, 'ALTER TABLE public.users OWNER TO openhabit;');

    DB::shouldReceive('statement')->once()->with('DROP SCHEMA public CASCADE');
    DB::shouldReceive('statement')->once()->withArgs(fn (string $s): bool => str_contains($s, 'CREATE SCHEMA public AUTHORIZATION'));

    Process::fake(['*psql*' => Process::result(exitCode: 0)]);

    $this->artisan('app:import-dump', ['--file' => $tmpFile])
        ->expectsOutputToContain('Dropping and recreating public schema')
        ->expectsOutputToContain('Importing dump')
        ->expectsOutputToContain('Import complete')
        ->assertExitCode(0);

    unlink($tmpFile);
});

it('removes \\restrict and \\unrestrict metacommands from sql', function (): void {
    $sql = "\\restrict abc123\nCREATE TABLE t (id int);\n\\unrestrict\nSELECT 1;";

    $tmpFile = (string) tempnam(sys_get_temp_dir(), 'test_');
    file_put_contents($tmpFile, $sql);

    DB::shouldReceive('statement')->twice();
    Process::fake(['*psql*' => Process::result(exitCode: 0)]);

    $this->artisan('app:import-dump', ['--file' => $tmpFile])->assertExitCode(0);

    Process::assertRan(fn ($p): bool => ! str_contains((string) $p->input, 'restrict'));

    unlink($tmpFile);
});

it('replaces owner names but keeps FROM stdin intact', function (): void {
    $capturedSql = null;

    $sql = implode("\n", [
        'ALTER SCHEMA public OWNER TO openhabit;',
        'GRANT ALL ON TABLES TO openhabit;',
        'ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON TABLES TO openhabit;',
        'COPY public.users (id) FROM stdin;',
        '1',
        '\\.',
    ]);

    $tmpFile = (string) tempnam(sys_get_temp_dir(), 'test_');
    file_put_contents($tmpFile, $sql);

    DB::shouldReceive('statement')->twice();

    Process::fake(['*psql*' => function ($process) use (&$capturedSql) {
        $capturedSql = $process->input;

        return Process::result(exitCode: 0);
    }]);

    $this->artisan('app:import-dump', ['--file' => $tmpFile])->assertExitCode(0);

    $dbUser = config('database.connections.pgsql.username');

    expect($capturedSql)
        ->toContain('OWNER TO '.$dbUser)
        ->toContain('FOR ROLE '.$dbUser)
        ->toContain('FROM stdin')
        ->not->toContain('OWNER TO openhabit')
        ->not->toContain('FOR ROLE postgres');

    unlink($tmpFile);
});

it('returns failure when psql exits with non-zero code', function (): void {
    $tmpFile = (string) tempnam(sys_get_temp_dir(), 'test_');
    file_put_contents($tmpFile, 'SELECT 1;');

    DB::shouldReceive('statement')->twice();
    Process::fake(['*psql*' => Process::result(exitCode: 1)]);

    $this->artisan('app:import-dump', ['--file' => $tmpFile])
        ->expectsOutputToContain('Import failed')
        ->assertExitCode(1);

    unlink($tmpFile);
});
