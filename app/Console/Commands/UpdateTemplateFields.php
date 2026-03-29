<?php

namespace App\Console\Commands;

use App\Models\Template;
use App\Models\TemplateField;
use App\Support\TemplateFieldPreset;
use Illuminate\Console\Command;

class UpdateTemplateFields extends Command
{
    protected $signature = 'template:update-fields {template_id} {--preset=wedding} {--force}';
    protected $description = 'Update template fields to use standardized preset';

    public function handle()
    {
        $templateId = $this->argument('template_id');
        $preset = $this->option('preset');
        $force = $this->option('force');

        $template = Template::find($templateId);
        
        if (!$template) {
            $this->error("Template ID {$templateId} not found!");
            return 1;
        }

        $this->info("Template: {$template->name}");
        $this->info("Current fields: " . $template->fields()->count());
        
        $presetFields = TemplateFieldPreset::get($preset);
        
        if (empty($presetFields)) {
            $this->error("Preset '{$preset}' not found!");
            $this->info("Available presets: " . implode(', ', array_keys(TemplateFieldPreset::all())));
            return 1;
        }

        $this->info("New preset: {$preset} (" . count($presetFields) . " fields)");
        
        if (!$force && !$this->confirm('This will replace all existing fields. Continue?')) {
            $this->info('Cancelled.');
            return 0;
        }

        // Delete existing fields
        $this->info('Deleting existing fields...');
        $template->fields()->delete();

        // Create new fields from preset
        $this->info('Creating new fields from preset...');
        $bar = $this->output->createProgressBar(count($presetFields));
        $bar->start();

        foreach ($presetFields as $fieldData) {
            TemplateField::create([
                'template_id' => $template->id,
                'key' => $fieldData['key'],
                'label' => $fieldData['label'],
                'type' => $fieldData['type'],
                'group' => $fieldData['group'],
                'required' => $fieldData['required'],
                'order' => $fieldData['order'],
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ Template fields updated successfully!");
        $this->info("Total fields: " . $template->fields()->count());

        return 0;
    }
}
