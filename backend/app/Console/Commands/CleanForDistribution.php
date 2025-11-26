<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanForDistribution extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-data {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean orders, bills, and transactional data for distribution, keeping configuration.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('This will delete all orders, bills, and transactional data. Are you sure?', $this->option('force'))) {
            $this->info('Operation cancelled.');
            return;
        }

        $tablesToTruncate = [
            'order_items',
            'orders',
            'bills',
            'kitchen_order_tickets',
            'salary_advances',
            'jobs',
            'cache',
            'cache_locks',
            'job_batches',
            'failed_jobs',
            'sessions',
        ];

        DB::statement('PRAGMA foreign_keys = OFF;');

        foreach ($tablesToTruncate as $table) {
            if (Schema::hasTable($table)) {
                $this->info("Truncating table: {$table}");
                DB::table($table)->truncate();
            } else {
                $this->warn("Table not found: {$table}");
            }
        }

        DB::statement('PRAGMA foreign_keys = ON;');

        $this->info('Data cleanup completed successfully. Ready for distribution.');
    }
}
