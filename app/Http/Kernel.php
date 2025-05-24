<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Uygulamanın global HTTP middleware yığını.
     * Bu middleware'ler her HTTP isteğinde çalışır.
     * 
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,  // Güvenilir host'ları tanımlar
        \App\Http\Middleware\TrustProxies::class,  // Proxy sunucuları güvenilir olarak işaretler
        \Illuminate\Http\Middleware\HandleCors::class,  // CORS (Cross-Origin Resource Sharing) isteklerini yönetir
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,  // Bakım modu sırasında istekleri engeller
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,  // POST isteklerinin boyutunu kontrol eder
        \App\Http\Middleware\TrimStrings::class,  // Gelen string verilerdeki boşlukları temizler
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,  // Boş stringleri null'a çevirir
        \App\Http\Middleware\ContentSecurityPolicy::class,  // İçerik güvenlik politikasını yönetir
    ];

    /**
     * Uygulamanın rota middleware grupları.
     * Bu gruplar belirli rota gruplarına uygulanır.
     * 
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,  // Çerezleri şifreler
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,  // Kuyruğa alınmış çerezleri yanıta ekler
            \Illuminate\Session\Middleware\StartSession::class,  // Oturumu başlatır
            // \Illuminate\Session\Middleware\AuthenticateSession::class,  // Oturum kimlik doğrulaması
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,  // Session'daki hataları view'lara paylaşır
            \App\Http\Middleware\VerifyCsrfToken::class,  // CSRF token doğrulaması yapar
            \Illuminate\Routing\Middleware\SubstituteBindings::class,  // Rota parametrelerini model örneklerine bağlar
            \App\Http\Middleware\HandleInertiaRequests::class,  // Inertia.js isteklerini yönetir
        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,  // Frontend isteklerinin durumunu yönetir
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',  // API isteklerini sınırlar
            \Illuminate\Routing\Middleware\SubstituteBindings::class,  // Rota parametrelerini model örneklerine bağlar
        ],
    ];

    /**
     * Uygulamanın middleware takma adları.
     * Bu takma adlar, rotalara ve gruplara middleware atamak için kullanılır.
     * 
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,  // Kimlik doğrulama
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,  // Temel kimlik doğrulama
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,  // Oturum kimlik doğrulaması
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,  // Önbellek başlıklarını ayarlar
        'can' => \Illuminate\Auth\Middleware\Authorize::class,  // Yetkilendirme
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,  // Misafir kullanıcı kontrolü
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,  // Şifre onayı
        'signed' => \App\Http\Middleware\ValidateSignature::class,  // İmza doğrulaması
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,  // İstek sınırlama
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,  // E-posta doğrulama
        'admin' => \App\Http\Middleware\AdminMiddleware::class,  // Admin yetkisi kontrolü
    ];
} 