<?php

namespace App\Console\Commands;

use App\Models\Invitation;
use Illuminate\Console\Command;

class TestLiveEditInjection extends Command
{
    protected $signature = 'test:live-edit-injection {invitation_id}';
    protected $description = 'Test live edit injection for an invitation';

    public function handle()
    {
        $invitationId = $this->argument('invitation_id');
        $invitation = Invitation::with(['data.templateField', 'template', 'gallery', 'bankAccounts'])->findOrFail($invitationId);
        
        $data = $invitation->getDataMap();
        $gallery = $invitation->gallery;
        
        // Render template
        $templateContent = view($invitation->template->viewPath(), compact('invitation', 'data', 'gallery'))->render();
        
        $this->info("Original HTML length: " . strlen($templateContent));
        $this->info("Has <head> tag: " . (strpos($templateContent, '<head>') !== false ? 'YES' : 'NO'));
        $this->info("Has </head> tag: " . (strpos($templateContent, '</head>') !== false ? 'YES' : 'NO'));
        $this->info("Has <body tag: " . (strpos($templateContent, '<body') !== false ? 'YES' : 'NO'));
        
        // Inject
        $injected = $this->injectLiveEditSupport($templateContent, $invitation);
        
        $this->info("\nAfter injection:");
        $this->info("Injected HTML length: " . strlen($injected));
        $this->info("Has csrf-token: " . (strpos($injected, 'csrf-token') !== false ? 'YES' : 'NO'));
        $this->info("Has live-edit.js: " . (strpos($injected, 'live-edit.js') !== false ? 'YES' : 'NO'));
        $this->info("Has data-invitation-id: " . (strpos($injected, 'data-invitation-id') !== false ? 'YES' : 'NO'));
        
        // Show snippet
        $this->newLine();
        $this->info("Head section snippet:");
        preg_match('/<head[^>]*>.*?<\/head>/is', $injected, $matches);
        if ($matches) {
            $this->line(substr($matches[0], 0, 500));
        }
        
        $this->newLine();
        $this->info("Body tag snippet:");
        preg_match('/<body[^>]*>/i', $injected, $bodyMatches);
        if ($bodyMatches) {
            $this->line($bodyMatches[0]);
        }
    }
    
    private function injectLiveEditSupport(string $html, Invitation $invitation): string
    {
        // Add CSRF token if not present
        if (strpos($html, 'name="csrf-token"') === false) {
            $csrfMeta = '<meta name="csrf-token" content="' . csrf_token() . '">';
            $html = preg_replace('/(<head[^>]*>)/i', '$1' . $csrfMeta, $html);
        }
        
        // Add live edit script before </head>
        $liveEditScript = '<script src="' . asset('assets/js/live-edit.js') . '" defer></script>';
        $html = preg_replace('/(<\/head>)/i', $liveEditScript . '$1', $html);
        
        // Add data attributes to body tag
        $dataAttrs = 'data-invitation-id="' . $invitation->id . '" data-is-owner="true"';
        $html = preg_replace('/(<body[^>]*)(>)/i', '$1 ' . $dataAttrs . '$2', $html);
        
        return $html;
    }
}
