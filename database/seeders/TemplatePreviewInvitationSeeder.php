<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Template;
use App\Models\Invitation;

class TemplatePreviewInvitationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Update preview_url untuk setiap template menggunakan invitation yang sudah publish
     */
    public function run(): void
    {
        $templates = Template::all();

        foreach ($templates as $template) {
            // Cari invitation yang sudah published untuk template ini
            $publishedInvitation = Invitation::where('template_id', $template->id)
                ->where('status', 'published')
                ->first();

            if (!$publishedInvitation) {
                $this->command->warn("⚠ No published invitation found for template '{$template->name}'. Skipping...");
                continue;
            }

            // Update template preview_url menggunakan route() helper
            $previewUrl = route('invitation.show', $publishedInvitation->slug);
            
            $template->update([
                'preview_url' => $previewUrl
            ]);

            $this->command->info("✓ Updated preview_url for '{$template->name}'");
            $this->command->info("  → {$previewUrl}");
        }

        $this->command->info('');
        $this->command->info('✓ All template preview URLs updated successfully!');
        $this->command->info('');
        $this->command->info('Note: Templates without published invitations were skipped.');
        $this->command->info('Admin can create and publish invitations for those templates in the dashboard.');
    }
}
