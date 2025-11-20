<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdatePaidBillsAmounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:update-paid-amounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update amount_paid for all paid bills to match total_amount';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = \App\Models\Bill::where('status', 'paid')
            ->whereColumn('amount_paid', '!=', 'total_amount')
            ->orWhereNull('amount_paid')
            ->update([
                'amount_paid' => \DB::raw('total_amount')
            ]);

        $this->info("Updated $count paid bills with amount_paid = total_amount");
    }
}
