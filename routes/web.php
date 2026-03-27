<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\InvitationController;

// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Halaman publik undangan (tanpa auth)
Route::get('/inv/{slug}', [InvitationController::class, 'show'])->name('invitation.show');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Undangan
    Route::get('invitations', [InvitationController::class, 'index'])->name('invitations.index');
    Route::get('invitations/select-template', [InvitationController::class, 'selectTemplate'])->name('invitations.select-template');
    Route::get('invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('invitations', [InvitationController::class, 'store'])->name('invitations.store');
    Route::get('invitations/{invitation}/edit', [InvitationController::class, 'edit'])->name('invitations.edit');
    Route::put('invitations/{invitation}', [InvitationController::class, 'update'])->name('invitations.update');
    Route::delete('invitations/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');
    Route::get('invitations/{invitation}/preview', [InvitationController::class, 'preview'])->name('invitations.preview');
    Route::post('invitations/{invitation}/publish', [InvitationController::class, 'publish'])->name('invitations.publish');
    Route::post('invitations/{invitation}/unpublish', [InvitationController::class, 'unpublish'])->name('invitations.unpublish');

    // Template Management (admin only)
    Route::resource('templates', TemplateController::class)->middleware([
        'index'   => 'can:view-templates',
        'create'  => 'can:create-templates',
        'store'   => 'can:create-templates',
        'edit'    => 'can:edit-templates',
        'update'  => 'can:edit-templates',
        'destroy' => 'can:delete-templates',
    ]);
    Route::post('templates/{template}/fields', [TemplateController::class, 'storeField'])->name('templates.fields.store')->middleware('can:edit-templates');
    Route::delete('templates/{template}/fields/{field}', [TemplateController::class, 'destroyField'])->name('templates.fields.destroy')->middleware('can:edit-templates');

    // User Management
    Route::resource('users', UserController::class)->middleware([
        'index'   => 'can:view-users',
        'create'  => 'can:create-users',
        'store'   => 'can:create-users',
        'edit'    => 'can:edit-users',
        'update'  => 'can:edit-users',
        'destroy' => 'can:delete-users',
    ]);

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
});
