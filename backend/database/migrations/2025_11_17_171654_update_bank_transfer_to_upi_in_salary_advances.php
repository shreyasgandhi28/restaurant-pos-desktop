<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing bank_transfer entries to upi
        \DB::table('salary_advances')
            ->where('payment_method', 'bank_transfer')
            ->update(['payment_method' => 'upi']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert upi entries back to bank_transfer
        \DB::table('salary_advances')
            ->where('payment_method', 'upi')
            ->update(['payment_method' => 'bank_transfer']);
    }
};
