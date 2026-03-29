<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('love_story_timeline', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->onDelete('cascade');
            $table->string('sender'); // 'groom' atau 'bride'
            $table->text('message')->nullable(); // Nullable karena timeskip tidak perlu message
            $table->boolean('is_timeskip')->default(false); // Apakah ini timeskip separator
            $table->string('timeskip_label')->nullable(); // Label untuk timeskip (e.g., "3 bulan kemudian...")
            $table->date('event_date')->nullable();
            $table->time('event_time')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['invitation_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('love_story_timeline');
    }
};
