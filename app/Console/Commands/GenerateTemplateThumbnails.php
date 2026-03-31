<?php

namespace App\Console\Commands;

use App\Models\Template;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

class GenerateTemplateThumbnails extends Command
{
    protected $signature = 'templates:generate-thumbnails 
                            {--template= : Specific template slug to generate}
                            {--force : Force regenerate even if thumbnail exists}';

    protected $description = 'Generate thumbnails for templates from their preview URLs using Browsershot';

    public function handle()
    {
        $templateSlug = $this->option('template');
        $force = $this->option('force');

        if ($templateSlug) {
            $templates = Template::where('slug', $templateSlug)->get();
            if ($templates->isEmpty()) {
                $this->error("Template with slug '{$templateSlug}' not found!");
                return 1;
            }
        } else {
            $templates = Template::where('is_active', true)->get();
        }

        if ($templates->isEmpty()) {
            $this->warn('No templates found to process.');
            return 0;
        }

        $this->info("Found {$templates->count()} template(s) to process...\n");

        foreach ($templates as $template) {
            if ($template->thumbnail && !$force) {
                $this->line("⏭️  Skipping '{$template->name}' - thumbnail already exists");
                continue;
            }

            $this->info("📸 Processing: {$template->name}");
            $this->generateScreenshot($template);
        }

        $this->newLine();
        $this->info('✅ Thumbnail generation completed!');
        return 0;
    }

    protected function generateScreenshot(Template $template)
    {
        if (!$template->preview_url) {
            $this->warn("   ⚠️  No preview URL found for '{$template->name}'");
            return;
        }

        try {
            $filename = "thumbnails/{$template->slug}.jpg";
            $fullPath = storage_path("app/public/{$filename}");
            
            // Create thumbnails directory if not exists
            $thumbnailDir = storage_path('app/public/thumbnails');
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            // Remove ?to=demo-user parameter from URL to show actual cover page
            $screenshotUrl = preg_replace('/\?to=[^&]*(&|$)/', '', $template->preview_url);
            $screenshotUrl = rtrim($screenshotUrl, '?&');

            $this->line("   🌐 URL: {$screenshotUrl}");
            $this->line("   ⏳ Generating screenshot...");

            try {
                // Generate screenshot using Browsershot
                $browsershot = \Spatie\Browsershot\Browsershot::url($screenshotUrl)
                    ->windowSize(1200, 800)
                    ->setScreenshotType('jpeg', 85)
                    ->waitUntilNetworkIdle()
                    ->dismissDialogs()
                    ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox'])
                    ->setDelay(2000); // Wait for page to fully load and auto-click

                $browsershot->save($fullPath);
                
                $fileSize = round(filesize($fullPath) / 1024, 2);
                $this->info("   ✅ Thumbnail saved: {$filename} ({$fileSize} KB)");
                
                $template->update(['thumbnail' => $filename]);
                
            } catch (\Exception $e) {
                $this->error("   ❌ Error: " . $e->getMessage());
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Error: " . $e->getMessage());
        }
    }
}
