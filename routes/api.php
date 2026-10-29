<?php

use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AdminRoleController;
use App\Http\Controllers\Api\Auth\AdminAuthController;
use App\Http\Controllers\Api\BlogCategoryController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CategoryController;

use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\MenuItemController;
use App\Http\Controllers\Api\NewsCategoryController;
use App\Http\Controllers\Api\NewsController;

use App\Http\Controllers\Api\PageController;

use App\Http\Controllers\Api\PostController;

use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\SliderController;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/* Admin Authentication Routes */

Route::prefix('admin')->controller(AdminAuthController::class)->group(function () {
    Route::post('login', 'login')->name('admin.login');

    Route::middleware('auth:user')->group(function () {
        Route::get('me', 'me')->name('admin.me');
        Route::get('profile', 'profile')->name('admin.profile');
        Route::post('logout', 'logout')->name('admin.logout');
    });
});
Route::get('/dashboard', [AdminDashboardController::class, 'index'])->middleware('auth:user');
/* Admin Users & Role/Permission Management */
Route::middleware(['auth:user'])->controller(AdminRoleController::class)->group(function () {
    Route::get('admins', 'index');
    Route::get('admins/{admin}', 'show');
    Route::put('admins/{admin}', 'updateUser')->middleware('permission:update-users');
    Route::put('admins/{admin}/role', 'updateUserRole')->middleware('permission:update-users');
    Route::post('admins', 'store')->middleware('permission:create-users');
    Route::delete('admins/{admin}', 'destroy')->middleware('permission:delete-users');

    // Role & Permissions
    Route::get('roles', 'roles');
    Route::get('roles-with-permissions', 'rolesWithPermissions');
    Route::put('roles/{role}/permissions', 'updatePermissions')->middleware('permission:update-permissions');
    Route::get('permissions', 'permissions');
});



/* Frontend Sections & Sliders */
Route::prefix('sections')->middleware(['auth:user', 'permission:manage-frontend'])->controller(SectionController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('{id}', 'show')->name('sections.show');
    Route::put('{id}', 'update')->name('sections.update');
});

Route::prefix('sliders')->middleware(['auth:user', 'permission:manage-frontend'])->controller(SliderController::class)->group(function () {
    Route::get('/{key}', 'bannerSliders');
    Route::get('/{id}/show', 'show');
    Route::post('/', 'store');
    Route::post('/{id}', 'update');
    Route::patch('/{id}/toggle', 'toggle')->name('sliders.toggle');
    Route::delete('/{id}', 'destroy');
});

/* Global Settings */
Route::middleware('auth:user')->prefix('settings')->controller(SettingsController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/update', 'update');
    Route::get('/security', 'getSecuritySettings');
});



/* Menus & Menu Items */
Route::prefix('menus')->middleware(['auth:user', 'permission:manage-menus'])->controller(MenuController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::prefix('{menu}')->group(function () {
        Route::get('/', 'show');
        Route::put('/', 'update');
        Route::delete('/', 'destroy');
    });
});

Route::prefix('menu-items')->middleware(['auth:user', 'permission:manage-menus'])->controller(MenuItemController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('{menuItem}', 'show');
    Route::put('{menuItem}', 'update');
    Route::delete('{menuItem}', 'destroy');
    Route::post('reorder', 'reorder');
});

/* Media Management */
Route::middleware(['auth:user'])->prefix('media')->controller(MediaController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/upload', 'upload');
    Route::delete('/file', 'deleteFile');
});



/* Utility: List available frontend template files */
Route::middleware(['auth:user'])->get('templates', function () {
    $path = resource_path('views/layouts/frontend');
    $files = [];

    if (File::exists($path)) {
        $files = collect(File::files($path))
            ->filter(fn($file) => Str::endsWith($file->getFilename(), '.blade.php'))
            ->map(fn($file) => Str::replaceLast('.blade', '', pathinfo($file->getFilename(), PATHINFO_FILENAME)))
            ->values()
            ->toArray();
    }

    return response()->json($files);
});
