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
        Schema::table('payment_channels', function (Blueprint $table) {
            // DOKU Configuration Fields
            $table->string('billing_type', 20)->default('FIX_BILL')->after('bin_notes')->comment('Billing Type: FIX_BILL, OPEN_BILL, etc.');
            $table->string('feature', 20)->default('DGPC')->after('billing_type')->comment('Feature: DGPC, MGPC, DIPC');
            $table->string('bin_type', 50)->default('Doku General BIN')->after('feature')->comment('BIN Type description');
            $table->string('merchant_bin', 20)->nullable()->after('bin_type')->comment('Merchant BIN (same as bin field)');
            $table->string('partner_service_id', 20)->nullable()->after('merchant_bin')->comment('Partner Service ID (formatted BIN)');
            $table->string('prefix_customer_no', 20)->nullable()->after('partner_service_id')->comment('Prefix for Customer Number');
            $table->string('va_trx_type', 1)->default('C')->after('prefix_customer_no')->comment('VA Transaction Type: C (Closed), O (Open)');
        });
        
        // Update existing BNI record with complete configuration
        DB::table('payment_channels')
            ->where('code', 'VIRTUAL_ACCOUNT_BANK_BNI')
            ->update([
                'billing_type' => 'FIX_BILL',
                'feature' => 'DGPC',
                'bin_type' => 'Doku General BIN',
                'merchant_bin' => '988291723',
                'partner_service_id' => '988291723',
                'prefix_customer_no' => null,
                'va_trx_type' => 'C',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_channels', function (Blueprint $table) {
            $table->dropColumn([
                'billing_type',
                'feature',
                'bin_type',
                'merchant_bin',
                'partner_service_id',
                'prefix_customer_no',
                'va_trx_type',
            ]);
        });
    }
};
