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
        Schema::table('music_upload_orders', function (Blueprint $table) {
            // Drop foreign key first
            // $table->dropForeign(['music_id']);
            
            // Add new columns
            // $table->integer('qty')->default(1)->after('order_number');
            // $table->decimal('price_per_slot', 10, 2)->default(10000)->after('amount');
            // $table->decimal('admin_fee', 10, 2)->default(0)->after('price_per_slot');
            // $table->unsignedBigInteger('payment_channel_id')->nullable()->after('payment_method');
            // $table->string('va_number')->nullable()->after('payment_channel_id');
            // $table->string('payment_url')->nullable()->after('va_number');
            // $table->text('qr_string')->nullable()->after('payment_url');
            // $table->string('qr_url')->nullable()->after('qr_string');
            // $table->timestamp('expired_at')->nullable()->after('paid_at');
            
            // Modify existing columns
            $table->decimal('amount', 10, 2)->change();
            $table->enum('status', ['pending', 'paid', 'expired', 'cancelled'])->default('pending')->change();
            
            // Drop old columns that are no longer needed
            // $table->dropColumn(['temp_title', 'temp_artist', 'temp_file_path', 'music_id']);
            
            // Add foreign key for payment_channel_id
            // $table->foreign('payment_channel_id')->references('id')->on('payment_channels')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('music_upload_orders', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['payment_channel_id']);
            
            // Drop new columns
            $table->dropColumn([
                'qty',
                'price_per_slot',
                'admin_fee',
                'payment_channel_id',
                'va_number',
                'payment_url',
                'qr_string',
                'qr_url',
                'expired_at'
            ]);
            
            // Restore old columns
            $table->string('temp_title')->nullable();
            $table->string('temp_artist')->nullable();
            $table->string('temp_file_path')->nullable();
            $table->unsignedBigInteger('music_id')->nullable();
            
            // Restore old column types
            $table->integer('amount')->default(5000)->change();
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending')->change();
            
            // Restore foreign key
            $table->foreign('music_id')->references('id')->on('music')->onDelete('set null');
        });
    }
};
