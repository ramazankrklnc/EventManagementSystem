<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;

Route::get('/', [HomeController::class, 'home'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// Sepet Rotası (Güncellendi)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index')->middleware(['auth']);
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add')->middleware(['auth']);
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update')->middleware(['auth']);
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove')->middleware(['auth']);
Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout')->middleware(['auth']);

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        // 1. Yerel yayınlanmış etkinlik sayısı
        $localPublishedEventsCount = \App\Models\Event::where('is_published', true)->count();

        // 2. Aktif ve bizde yayınlanmış Ticketmaster etkinlik sayısı
        $ticketmasterService = app(\App\Services\TicketmasterService::class);
        // TicketmasterService'den gelen tüm aktif etkinlikler (İstanbul için)
        $allApiEventsFromService = $ticketmasterService->getEventsByCity('İstanbul', 'TR');

        $trulyPublishedTicketmasterEventsCount = 0;
        if (is_array($allApiEventsFromService)) {
            $trulyPublishedTicketmasterEventsCount = collect($allApiEventsFromService)->filter(function ($event) {
                if (empty($event['id'])) {
                    return false; // ID'si olmayanları atla
                }
                // ApiEventState modelinde bu ID'nin yayın durumunu kontrol et
                $state = \App\Models\ApiEventState::where('event_id', $event['id'])->first();
                return $state && $state->is_published;
            })->count();
        }
        
        // Ana sayfada gösterilmesi beklenen toplam yayınlanmış etkinlik sayısı
        $totalPublishedEvents = $localPublishedEventsCount + $trulyPublishedTicketmasterEventsCount;
        
        return view('admin.dashboard', compact('totalPublishedEvents'));
    })->name('dashboard');

    // Admin Event Routes
    Route::get('/events', [App\Http\Controllers\Admin\EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [App\Http\Controllers\Admin\EventController::class, 'create'])->name('events.create');
    Route::post('/events', [App\Http\Controllers\Admin\EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [App\Http\Controllers\Admin\EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [App\Http\Controllers\Admin\EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [App\Http\Controllers\Admin\EventController::class, 'destroy'])->name('events.destroy');
    Route::patch('/events/{event}/toggle-publish', [App\Http\Controllers\Admin\EventController::class, 'togglePublish'])->name('events.togglePublish');

    // API Event Routes
    Route::patch('/events/api/{eventId}/toggle-publish', [App\Http\Controllers\Admin\EventController::class, 'toggleApiEventPublish'])->name('events.toggleApiEventPublish');
    Route::get('/events/api/{eventId}/edit', [App\Http\Controllers\Admin\EventController::class, 'editApiEvent'])->name('events.editApiEvent');
    Route::put('/events/api/{eventId}', [App\Http\Controllers\Admin\EventController::class, 'updateApiEvent'])->name('events.updateApiEvent');

    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    
    // Duyuru Yönetimi Rotaları
    Route::resource('announcements', App\Http\Controllers\Admin\AnnouncementController::class);
    
    Route::post('/users/{user}/approve', [App\Http\Controllers\Admin\UserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{user}/make-admin', [App\Http\Controllers\Admin\UserController::class, 'makeAdmin'])->name('users.makeAdmin');
    Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/announcements-new-design', [\App\Http\Controllers\HomeController::class, 'indexNewDesign'])->name('announcements.index_new_design');
});

// Şifre değiştirme rotaları
Route::get('/password/change', [App\Http\Controllers\Auth\PasswordChangeController::class, 'showChangeForm'])->name('password.change');
Route::post('/password/change', [App\Http\Controllers\Auth\PasswordChangeController::class, 'changePassword']);

require __DIR__.'/auth.php';
