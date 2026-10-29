<?php

use App\Http\Controllers\FrontendController;
use App\Services\ImageService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

/* Utility route for clearing all caches */

Route::get('/clear', function () {
    Artisan::call('optimize:clear');

    // Remove the public/cache directory
    $cacheDir = public_path('cache');
    if (File::exists($cacheDir)) {
        File::deleteDirectory($cacheDir);
    }

    return "Application cache cleared!";
});

/* Frontend Routes - Public website pages */
Route::controller(FrontendController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('match-fixtures', 'matchFixtures')->name('match-fixtures');




    Route::post('contact-us', 'contactSubmit')->name('contact.submit');



    /* Dynamic image resize & cache route (returns file directly) */
    Route::get('/image/{width}/{height}/{format}/{path}', function ($width, $height, $format, $path) {
        $fullPath = $path;
        $url = ImageService::resizeAndCache($fullPath, (int) $width, (int) $height, $format);

        // Convert URL back to actual file path
        $filePath = public_path(str_replace(asset(''), '', $url));

        // Return raw file with correct headers
        return Response::file($filePath, [
            'Content-Type'  => 'image/' . $format,
            'Cache-Control' => 'public, max-age=604800' // 1 week cache
        ]);
    })->where('path', '.*');

    /* Image resize redirect route (legacy support) */
    Route::get('/image_url/{width}/{height}/{format}/{path}', function ($width, $height, $format, $path) {
        $fullPath = $path;
        $url = ImageService::resizeAndCache($fullPath, (int) $width, (int) $height, $format);

        return redirect($url);
    })->where('path', '.*');
});

/* Admin panel routes */

/* Catch-all route for Vue SPA admin panel */
Route::get('admin/{any?}', function () {
    return view('welcome');
})->where('any', '.*');
