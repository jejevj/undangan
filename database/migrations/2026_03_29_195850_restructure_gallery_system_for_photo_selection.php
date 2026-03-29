<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create user_gallery_photos table (photo pool per user)
        if (!Schema::hasTable('user_gallery_photos')) {
            Schema::create('user_gallery_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('path'); // storage path
                $table->string('caption')->nullable();
                $table->boolean('is_paid')->default(false); // false = slot gratis, true = slot berbayar
                $table->timestamps();
                
                $table->index('user_id');
            });
        }

        // Migrate existing data from invitation_gallery to user_gallery_photos
        // First, get all unique photos per user
        $existingPhotos = DB::table('invitation_gallery')
            ->join('invitations', 'invitation_gallery.invitation_id', '=', 'invitations.id')
            ->select('invitations.user_id', 'invitation_gallery.path', 'invitation_gallery.caption', 'invitation_gallery.is_paid')
            ->distinct()
            ->get();

        foreach ($existingPhotos as $photo) {
            DB::table('user_gallery_photos')->insertOrIgnore([
                'user_id' => $photo->user_id,
                'path' => $photo->path,
                'caption' => $photo->caption,
                'is_paid' => $photo->is_paid,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Add photo_id column to invitation_gallery if not exists
        if (!Schema::hasColumn('invitation_gallery', 'photo_id')) {
            Schema::table('invitation_gallery', function (Blueprint $table) {
                $table->unsignedBigInteger('photo_id')->nullable()->after('invitation_id');
            });
        }

        // Populate photo_id by matching user_id and path
        $invitationGalleries = DB::table('invitation_gallery')
            ->join('invitations', 'invitation_gallery.invitation_id', '=', 'invitations.id')
            ->select('invitation_gallery.id', 'invitations.user_id', 'invitation_gallery.path')
            ->get();

        foreach ($invitationGalleries as $ig) {
            $photoId = DB::table('user_gallery_photos')
                ->where('user_id', $ig->user_id)
                ->where('path', $ig->path)
                ->value('id');
            
            if ($photoId) {
                DB::table('invitation_gallery')
                    ->where('id', $ig->id)
                    ->update(['photo_id' => $photoId]);
            }
        }

        // Now drop old columns and add foreign key
        Schema::table('invitation_gallery', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['user_id']);
            $table->dropColumn(['path', 'caption', 'is_paid', 'user_id']);
            $table->unsignedBigInteger('photo_id')->nullable(false)->change();
            $table->foreign('photo_id')->references('id')->on('user_gallery_photos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Restore invitation_gallery structure
        Schema::table('invitation_gallery', function (Blueprint $table) {
            $table->string('path')->after('invitation_id');
            $table->string('caption')->nullable()->after('path');
            $table->boolean('is_paid')->default(false)->after('caption');
            
            $table->dropForeign(['photo_id']);
            $table->dropColumn('photo_id');
        });

        // Restore data from user_gallery_photos back to invitation_gallery
        DB::statement("
            UPDATE invitation_gallery ig
            INNER JOIN user_gallery_photos ugp ON ig.photo_id = ugp.id
            SET ig.path = ugp.path,
                ig.caption = ugp.caption,
                ig.is_paid = ugp.is_paid
        ");

        // Drop user_gallery_photos table
        Schema::dropIfExists('user_gallery_photos');
    }
};
