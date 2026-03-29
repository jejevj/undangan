<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gallery_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_channel_id')->nullable()->after('payment_method');
            $table->decimal('admin_fee', 10, 2)->default(0)->after('price_per_photo');
            $table->string('va_number')->nullable()->after('payment_channel_id');
            $table->string('payment_url')->nullable()->after('va_number');
            $table->text('qr_string')->nullable()->after('payment_url');
            $table->string('qr_url')->nullable()->after('qr_string');
            $table->timestamp('expired_at')->nullable()->after('paid_at');
            
            $table->foreign('payment_channel_id')->references('id')->on('payment_channels')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('gallery_orders', function (Blueprint $table) {
            $table->dropForeign(['payment_channel_id']);
            $table->dropColumn([
                'payment_channel_id',
                'admin_fee',
                'va_number',
                'payment_url',
                'qr_string',
                'qr_url',
                'expired_at',
            ]);
        });
    }
};
