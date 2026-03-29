<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TemplateCategoryController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\LandingController;

use App\Http\Controllers\GuestController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GalleryCheckoutController;
use App\Http\Controllers\GiftController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PricingPlanController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\GeneralConfigController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\PaymentGatewayConfigController;
use App\Http\Controllers\Webhook\DokuWebhookController;

// DOKU Webhook (no CSRF protection)
Route::post('/webhook/doku/payment-notification', [DokuWebhookController::class, 'handlePaymentNotification'])
    ->name('webhook.doku.payment-notification');

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Landing Page
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/templates', [LandingController::class, 'getTemplates'])->name('landing.templates');

// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register')->middleware('guest');
Route::post('/register', [RegisterController::class, 'register'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes with /dash prefix
Route::prefix('dash')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Subscription / Pricing
    Route::get('subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::get('subscription/{plan}/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::post('subscription/{order}/create-va', [SubscriptionController::class, 'createVirtualAccount'])->name('subscription.create-va');
    Route::post('subscription/{order}/create-ewallet', [SubscriptionController::class, 'createEWalletPayment'])->name('subscription.create-ewallet');
    Route::post('subscription/{order}/create-qris', [SubscriptionController::class, 'createQrisPayment'])->name('subscription.create-qris');
    Route::get('subscription/{order}/ewallet-success', [SubscriptionController::class, 'ewalletSuccess'])->name('subscription.ewallet-success');
    Route::get('subscription/{order}/ewallet-failed', [SubscriptionController::class, 'ewalletFailed'])->name('subscription.ewallet-failed');
    Route::post('subscription/{subscription}/pay', [SubscriptionController::class, 'simulatePay'])->name('subscription.pay');
    
    // Payment Status Check API
    Route::post('api/payment/check-va-status', [\App\Http\Controllers\Api\PaymentStatusController::class, 'checkVAStatus'])->name('api.payment.check-va-status');
    Route::post('api/payment/check-ewallet-status', [\App\Http\Controllers\Api\PaymentStatusController::class, 'checkEWalletStatus'])->name('api.payment.check-ewallet-status');
    Route::post('api/payment/check-qris-status', [\App\Http\Controllers\Api\PaymentStatusController::class, 'checkQrisStatus'])->name('api.payment.check-qris-status');

    // Undangan
    Route::get('invitations', [InvitationController::class, 'index'])->name('invitations.index');
    Route::get('invitations/select-template', [InvitationController::class, 'selectTemplate'])->name('invitations.select-template');
    Route::get('invitations/templates', [InvitationController::class, 'getTemplates'])->name('invitations.templates');
    Route::get('invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('invitations', [InvitationController::class, 'store'])->name('invitations.store');
    Route::get('invitations/{invitation}/edit', [InvitationController::class, 'edit'])->name('invitations.edit');
    Route::put('invitations/{invitation}', [InvitationController::class, 'update'])->name('invitations.update');
    Route::delete('invitations/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');
    Route::get('invitations/{invitation}/preview', [InvitationController::class, 'preview'])->name('invitations.preview');
    Route::post('invitations/{invitation}/publish', [InvitationController::class, 'publish'])->name('invitations.publish');
    Route::post('invitations/{invitation}/unpublish', [InvitationController::class, 'unpublish'])->name('invitations.unpublish');
    
    // Live Edit API
    Route::post('api/invitations/{invitation}/live-edit', [\App\Http\Controllers\Api\LiveEditController::class, 'updateField'])->name('api.invitations.live-edit');
    Route::get('api/invitations/{invitation}/live-edit/{fieldKey}', [\App\Http\Controllers\Api\LiveEditController::class, 'getField'])->name('api.invitations.live-edit.get');
    Route::get('api/invitations/{invitation}/live-edit-fields', [\App\Http\Controllers\Api\LiveEditController::class, 'getAllFields'])->name('api.invitations.live-edit.fields');
    Route::get('api/invitations/{invitation}/live-edit-photos', [\App\Http\Controllers\Api\LiveEditController::class, 'getUserPhotos'])->name('api.invitations.live-edit.photos');
    Route::get('api/invitations/{invitation}/live-edit-music', [\App\Http\Controllers\Api\LiveEditController::class, 'getUserMusic'])->name('api.invitations.live-edit.music');
    Route::get('api/invitations/{invitation}/live-edit-gallery', [\App\Http\Controllers\Api\LiveEditController::class, 'getGalleryPhotos'])->name('api.invitations.live-edit.gallery');
    Route::post('api/invitations/{invitation}/live-edit-gallery', [\App\Http\Controllers\Api\LiveEditController::class, 'updateGallerySelection'])->name('api.invitations.live-edit.gallery.update');

    // Guest Management (nested under invitation)
    Route::get('invitations/{invitation}/guests', [GuestController::class, 'index'])->name('invitations.guests.index');
    Route::post('invitations/{invitation}/guests', [GuestController::class, 'store'])->name('invitations.guests.store');
    Route::put('invitations/{invitation}/guests/{guest}', [GuestController::class, 'update'])->name('invitations.guests.update');
    Route::delete('invitations/{invitation}/guests/{guest}', [GuestController::class, 'destroy'])->name('invitations.guests.destroy');
    Route::get('invitations/{invitation}/guests/{guest}/greeting', [GuestController::class, 'greeting'])->name('invitations.guests.greeting');

    // Gift / Bank Account Management
    Route::get('invitations/{invitation}/gift', [GiftController::class, 'index'])->name('invitations.gift.index');
    Route::post('invitations/{invitation}/gift', [GiftController::class, 'store'])->name('invitations.gift.store');
    Route::put('invitations/{invitation}/gift/{account}', [GiftController::class, 'update'])->name('invitations.gift.update');
    Route::delete('invitations/{invitation}/gift/{account}', [GiftController::class, 'destroy'])->name('invitations.gift.destroy');
    Route::get('invitations/{invitation}/gift/buy', [GiftController::class, 'buyFeature'])->name('invitations.gift.buy');
    Route::post('feature-orders/{order}/pay', [GiftController::class, 'simulatePay'])->name('gift.feature.pay');
    // Gallery Checkout (User-Level, not per invitation)
    Route::get('gallery/buy', [GalleryCheckoutController::class, 'selectQuantity'])->name('gallery.select-quantity');
    Route::get('gallery/checkout', [GalleryCheckoutController::class, 'checkout'])->name('gallery.checkout');
    Route::post('gallery-orders/{order}/create-va', [GalleryCheckoutController::class, 'createVA'])->name('gallery.create-va');
    Route::post('gallery-orders/{order}/create-qris', [GalleryCheckoutController::class, 'createQris'])->name('gallery.create-qris');
    Route::post('gallery/process-payment', [GalleryCheckoutController::class, 'processPayment'])->name('gallery.process-payment');
    Route::get('gallery-orders/{order}/payment', [GalleryCheckoutController::class, 'payment'])->name('gallery.payment');
    Route::get('gallery-orders/{order}/check-status', [GalleryCheckoutController::class, 'checkStatus'])->name('gallery.check-status');
    
    // Gallery Management (per invitation)
    Route::get('invitations/{invitation}/gallery', [GalleryController::class, 'index'])->name('invitations.gallery.index');
    Route::post('invitations/{invitation}/gallery', [GalleryController::class, 'store'])->name('invitations.gallery.store');
    Route::delete('invitations/{invitation}/gallery/{photo}', [GalleryController::class, 'destroy'])->name('invitations.gallery.destroy');
    Route::post('invitations/{invitation}/gallery/select', [GalleryController::class, 'selectPhotos'])->name('invitations.gallery.select');
    Route::post('invitations/{invitation}/gallery/update-order', [GalleryController::class, 'updateOrder'])->name('invitations.gallery.update-order');
    
    // Love Story Timeline Management
    Route::get('invitations/{invitation}/love-story', [\App\Http\Controllers\LoveStoryTimelineController::class, 'index'])->name('invitations.love-story.index');
    Route::post('invitations/{invitation}/love-story', [\App\Http\Controllers\LoveStoryTimelineController::class, 'store'])->name('invitations.love-story.store');
    Route::put('invitations/{invitation}/love-story/{timeline}', [\App\Http\Controllers\LoveStoryTimelineController::class, 'update'])->name('invitations.love-story.update');
    Route::delete('invitations/{invitation}/love-story/{timeline}', [\App\Http\Controllers\LoveStoryTimelineController::class, 'destroy'])->name('invitations.love-story.destroy');
    Route::post('invitations/{invitation}/love-story/update-order', [\App\Http\Controllers\LoveStoryTimelineController::class, 'updateOrder'])->name('invitations.love-story.update-order');
    Route::post('invitations/{invitation}/love-story/switch-mode', [\App\Http\Controllers\LoveStoryTimelineController::class, 'switchMode'])->name('invitations.love-story.switch-mode');
    
    // Old Gallery Routes (Keep for backward compatibility)
    Route::get('invitations/{invitation}/gallery/buy-slots', [GalleryController::class, 'buySlots'])->name('invitations.gallery.buy-slots');
    Route::post('invitations/{invitation}/gallery/buy-slots', [GalleryController::class, 'buySlots'])->name('invitations.gallery.buy-slots.post');
    Route::post('gallery-orders/{order}/pay', [GalleryController::class, 'simulatePay'])->name('gallery.pay');

    // Music Library (user)
    Route::get('music', [MusicController::class, 'index'])->name('music.index')->middleware('can:view-music');
    Route::get('music/{music}/buy', [MusicController::class, 'buy'])->name('music.buy')->middleware('can:view-music');
    Route::post('music/orders/{order}/pay', [MusicController::class, 'simulatePay'])->name('music.pay')->middleware('can:view-music');
    
    // Music Upload Slot Purchase
    Route::get('music-slots/buy', [\App\Http\Controllers\MusicUploadCheckoutController::class, 'selectQuantity'])->name('music.slots.buy');
    Route::get('music-slots/checkout', [\App\Http\Controllers\MusicUploadCheckoutController::class, 'checkout'])->name('music.slots.checkout');
    Route::post('music-upload-orders/{order}/create-va', [\App\Http\Controllers\MusicUploadCheckoutController::class, 'createVA'])->name('music.slots.create-va');
    Route::post('music-upload-orders/{order}/create-qris', [\App\Http\Controllers\MusicUploadCheckoutController::class, 'createQris'])->name('music.slots.create-qris');
    Route::get('music-upload-orders/{order}/check-status', [\App\Http\Controllers\MusicUploadCheckoutController::class, 'checkStatus'])->name('music.slots.check-status');
    
    // Music Upload Form
    Route::get('music/upload', [MusicController::class, 'uploadForm'])->name('music.upload')->middleware('can:upload-music');
    Route::post('music/upload', [MusicController::class, 'userUpload'])->name('music.upload.store')->middleware('can:upload-music');
    Route::get('music/upload/{order}/checkout', [MusicController::class, 'uploadCheckout'])->name('music.upload.checkout.old')->middleware('can:upload-music');
    Route::post('music/upload/{order}/pay', [MusicController::class, 'uploadPay'])->name('music.upload.pay')->middleware('can:upload-music');

    // Music Admin
    Route::middleware('can:manage-music')->group(function () {
        Route::get('admin/music', [MusicController::class, 'adminIndex'])->name('music.admin.index');
        Route::get('admin/music/create', [MusicController::class, 'adminCreate'])->name('music.admin.create');
        Route::post('admin/music', [MusicController::class, 'adminStore'])->name('music.admin.store');
        Route::delete('admin/music/{music}', [MusicController::class, 'adminDestroy'])->name('music.admin.destroy');
        Route::patch('admin/music/{music}/toggle', [MusicController::class, 'adminToggle'])->name('music.admin.toggle');
    });
    Route::resource('templates', TemplateController::class)->middleware([
        'index'   => 'can:view-templates',
        'create'  => 'can:create-templates',
        'store'   => 'can:create-templates',
        'edit'    => 'can:edit-templates',
        'update'  => 'can:edit-templates',
        'destroy' => 'can:delete-templates',
    ]);
    
    // Template Categories
    Route::resource('template-categories', TemplateCategoryController::class)->middleware([
        'index'   => 'can:view-templates',
        'create'  => 'can:create-templates',
        'store'   => 'can:create-templates',
        'edit'    => 'can:edit-templates',
        'update'  => 'can:edit-templates',
        'destroy' => 'can:delete-templates',
    ]);
    Route::post('templates/{template}/fields', [TemplateController::class, 'storeField'])->name('templates.fields.store')->middleware('can:edit-templates');
    
    // Template Categories
    Route::resource('template-categories', TemplateCategoryController::class)->middleware([
        'index'   => 'can:view-templates',
        'create'  => 'can:create-templates',
        'store'   => 'can:create-templates',
        'edit'    => 'can:edit-templates',
        'update'  => 'can:edit-templates',
        'destroy' => 'can:delete-templates',
    ]);
    Route::delete('templates/{template}/fields/{field}', [TemplateController::class, 'destroyField'])->name('templates.fields.destroy')->middleware('can:edit-templates');
    Route::patch('templates/{template}/toggle', [TemplateController::class, 'toggle'])->name('templates.toggle')->middleware('can:edit-templates');
    Route::post('templates/{template}/load-preset', [TemplateController::class, 'loadPreset'])->name('templates.load-preset')->middleware('can:edit-templates');

    // User Management
    Route::resource('users', UserController::class)->middleware([
        'index'   => 'can:view-users',
        'create'  => 'can:create-users',
        'store'   => 'can:create-users',
        'show'    => 'can:view-users',
        'edit'    => 'can:edit-users',
        'update'  => 'can:edit-users',
        'destroy' => 'can:delete-users',
    ]);
    Route::post('users/{user}/assign-plan', [UserController::class, 'assignPlan'])->name('users.assign-plan')->middleware('can:edit-users');
    Route::post('users/{user}/revoke-plan', [UserController::class, 'revokePlan'])->name('users.revoke-plan')->middleware('can:edit-users');

    // Role Management
    Route::resource('roles', RoleController::class)->middleware([
        'index'   => 'can:view-roles',
        'create'  => 'can:create-roles',
        'store'   => 'can:create-roles',
        'edit'    => 'can:edit-roles',
        'update'  => 'can:edit-roles',
        'destroy' => 'can:delete-roles',
    ]);

    // Permission Management
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index')->middleware('can:view-permissions');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store')->middleware('can:create-permissions');
    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy')->middleware('can:delete-permissions');

    // Menu Management
    Route::resource('menus', MenuController::class)->middleware([
        'index'   => 'can:view-menus',
        'create'  => 'can:create-menus',
        'store'   => 'can:create-menus',
        'edit'    => 'can:edit-menus',
        'update'  => 'can:edit-menus',
        'destroy' => 'can:delete-menus',
    ]);

    // Pricing Plan Management
    Route::resource('pricing-plans', PricingPlanController::class)->middleware([
        'index'   => 'can:view-pricing-plans',
        'create'  => 'can:create-pricing-plans',
        'store'   => 'can:create-pricing-plans',
        'edit'    => 'can:edit-pricing-plans',
        'update'  => 'can:edit-pricing-plans',
        'destroy' => 'can:delete-pricing-plans',
    ]);
    Route::patch('pricing-plans/{pricingPlan}/toggle', [PricingPlanController::class, 'toggle'])
        ->name('pricing-plans.toggle')
        ->middleware('can:edit-pricing-plans');

    // Partner Management
    Route::resource('partners', PartnerController::class)->middleware([
        'index'   => 'can:view-partners',
        'create'  => 'can:create-partners',
        'store'   => 'can:create-partners',
        'edit'    => 'can:edit-partners',
        'update'  => 'can:edit-partners',
        'destroy' => 'can:delete-partners',
    ]);

    // Payment Gateway Configuration
    Route::resource('payment-gateway', PaymentGatewayConfigController::class)->middleware([
        'index'   => 'can:payment-gateway.view',
        'show'    => 'can:payment-gateway.view',
        'create'  => 'can:payment-gateway.create',
        'store'   => 'can:payment-gateway.create',
        'edit'    => 'can:payment-gateway.edit',
        'update'  => 'can:payment-gateway.edit',
        'destroy' => 'can:payment-gateway.delete',
    ]);
    Route::post('payment-gateway/{paymentGateway}/test-connection', [PaymentGatewayConfigController::class, 'testConnection'])
        ->name('payment-gateway.test-connection')
        ->middleware('can:payment-gateway.view');
    
    // Debug signature (only in debug mode)
    if (config('app.debug')) {
        Route::get('payment-gateway/{paymentGateway}/debug-signature', [PaymentGatewayConfigController::class, 'debugSignature'])
            ->name('payment-gateway.debug-signature')
            ->middleware('can:payment-gateway.view');
    }

    // Payment Channels Management
    Route::prefix('admin/payment-channels')->name('admin.payment-channels.')->middleware('can:payment-channels.view')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PaymentChannelController::class, 'index'])->name('index');
        Route::patch('{channel}/toggle', [\App\Http\Controllers\Admin\PaymentChannelController::class, 'toggleActive'])->name('toggle')->middleware('can:payment-channels.edit');
        Route::patch('{channel}/update', [\App\Http\Controllers\Admin\PaymentChannelController::class, 'update'])->name('update')->middleware('can:payment-channels.edit');
        Route::post('{channel}/check', [\App\Http\Controllers\Admin\PaymentChannelController::class, 'checkAvailability'])->name('check')->middleware('can:payment-channels.check-availability');
        Route::post('check-all', [\App\Http\Controllers\Admin\PaymentChannelController::class, 'checkAllAvailability'])->name('check-all')->middleware('can:payment-channels.check-availability');
    });

    // General Config
    Route::get('general-config', [GeneralConfigController::class, 'index'])
        ->name('general-config.index')
        ->middleware('can:view-general-config');
    Route::put('general-config', [GeneralConfigController::class, 'update'])
        ->name('general-config.update')
        ->middleware('can:edit-general-config');

    // Funnel Analysis (Admin only)
    Route::get('admin/funnel-report', [\App\Http\Controllers\Admin\FunnelReportController::class, 'index'])
        ->name('admin.funnel-report');
});

// Halaman publik undangan (tanpa auth) - MUST BE LAST (catch-all route)
Route::get('/{slug}', [InvitationController::class, 'show'])->name('invitation.show');
Route::post('/{slug}/guest-message', [App\Http\Controllers\GuestMessageController::class, 'store'])->name('invitation.guest-message.store');
Route::post('/{slug}/guest-message/{message}/like', [App\Http\Controllers\GuestMessageController::class, 'like'])->name('invitation.guest-message.like');
