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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('restaurant_table_id')->nullable()->change();
            $table->enum('type', ['dining', 'takeaway', 'miscellaneous'])->default('dining')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        
        Schema::table('orders', function (Blueprint $table) {
            // We revert it to not nullable, assuming no null values exist or we accept potential error
            // In a real scenario we might want to handle data cleanup first
            $table->foreignId('restaurant_table_id')->nullable(false)->change();
        });
    }
};
