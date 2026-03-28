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
        Schema::table('doku_virtual_accounts', function (Blueprint $table) {
            // Update field lengths to accommodate DOKU format
            // VA Number = Partner Service ID (8) + Customer No (20) = 28 chars
            $table->string('customer_no', 30)->change(); // 20 → 30 for safety
            $table->string('virtual_account_no', 30)->change(); // 20 → 30 for safety
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doku_virtual_accounts', function (Blueprint $table) {
            $table->string('customer_no', 20)->change();
            $table->string('virtual_account_no', 20)->change();
        });
    }
};
