<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Template;
use App\Models\TemplateCategory;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = Sitemap::create();

        // Homepage - highest priority
        $sitemap->add(
            Url::create('/')
                ->setPriority(1.0)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
        );

        // Login & Register
        $sitemap->add(
            Url::create('/login')
                ->setPriority(0.5)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
        );

        // Add all published invitations (public pages)
        $invitations = Invitation::where('is_published', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($invitations as $invitation) {
            $sitemap->add(
                Url::create("/invitation/{$invitation->slug}")
                    ->setPriority(0.8)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setLastModificationDate($invitation->updated_at)
            );
        }

        // Add template categories if you have public template gallery
        $categories = TemplateCategory::where('is_active', true)->get();
        foreach ($categories as $category) {
            $sitemap->add(
                Url::create("/#templates?category={$category->slug}")
                    ->setPriority(0.6)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            );
        }

        return $sitemap->toResponse(request());
    }
}
