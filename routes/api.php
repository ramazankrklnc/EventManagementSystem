<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\AnnouncementController;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| API Rotaları
|--------------------------------------------------------------------------
|
| Burada uygulamanız için API rotalarını kaydedebilirsiniz.
| Bu rotalar RouteServiceProvider tarafından yüklenir ve
| hepsi "api" middleware grubuna atanır. Harika bir şeyler yapın!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Etkinlikler için API rotaları
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);
Route::get('/event-types', [EventController::class, 'getEventTypes']);

// Duyurular için API rotası
Route::get('/announcements', [AnnouncementController::class, 'index']);

// Ticketmaster etkinlikleri için API rotası
Route::get('/ticketmaster-events', [EventController::class, 'ticketmasterEvents']);

// Hava durumu için API rotası
Route::get('/weather', function () {
    $apiKey = env('WEATHER_API_KEY');
    $city = 'İstanbul';
    
    $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
        'q' => $city,
        'appid' => $apiKey,
        'units' => 'metric',
        'lang' => 'tr'
    ]);
    
    return $response->json();
}); 