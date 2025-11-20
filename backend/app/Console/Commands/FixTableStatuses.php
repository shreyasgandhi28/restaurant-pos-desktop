<?php

namespace App\Console\Commands;

use App\Models\RestaurantTable;
use Illuminate\Console\Command;

class FixTableStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tables:fix-statuses {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix table statuses based on active orders. Tables with active orders will be marked as occupied.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        $tables = RestaurantTable::all();
        $changes = 0;

        $this->info("Checking {$tables->count()} tables...");

        $progressBar = $this->output->createProgressBar($tables->count());
        $progressBar->start();

        foreach ($tables as $table) {
            $currentStatus = $table->status;
            $effectiveStatus = $table->effective_status;

            if ($currentStatus !== $effectiveStatus) {
                if ($dryRun) {
                    $this->newLine();
                    $this->line("Table {$table->table_number}: '{$currentStatus}' → '{$effectiveStatus}'");
                } else {
                    $table->update(['status' => $effectiveStatus]);
                }
                $changes++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info("DRY RUN: {$changes} tables would be updated");
        } else {
            $this->info("Fixed {$changes} table statuses");
        }

        if ($changes === 0) {
            $this->info('All table statuses are already correct!');
        }

        return 0;
    }
}
