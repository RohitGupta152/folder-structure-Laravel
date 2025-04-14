<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabaseConnectionTestCommand extends Command
{
    protected $signature = 'db:test';
    protected $description = 'Test database connection';

    public function handle()
    {
        try {
            DB::connection()->getPdo();
            $this->info('Database connection successful! 🟢');
            
            // Additional connection details
            $this->table('Connection Info', [
                ['Property', 'Value'],
                ['Database', DB::connection()->getDatabaseName()],
                ['Driver', DB::connection()->getDriverName()],
                ['Host', config('database.connections.mysql.host')],
            ]);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Database connection failed! 🔴');
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
