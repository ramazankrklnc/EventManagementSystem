<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Kimlik Doğrulama Varsayılanları
    |--------------------------------------------------------------------------
    |
    | Bu seçenek, uygulamanız için varsayılan kimlik doğrulama "guard"ını ve
    | şifre sıfırlama "broker"ını tanımlar. Gerektiğinde bu değerleri
    | değiştirebilirsiniz, ancak çoğu uygulama için başlangıçta uygundur.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Kimlik Doğrulama Guard'ları
    |--------------------------------------------------------------------------
    |
    | Burada, uygulamanız için tüm kimlik doğrulama guard'larını tanımlayabilirsiniz.
    | Sizin için oturum depolamasını ve Eloquent kullanıcı sağlayıcısını kullanan
    | harika bir varsayılan yapılandırma tanımlanmıştır.
    |
    | Tüm kimlik doğrulama guard'larının bir kullanıcı sağlayıcısı vardır, bu da
    | kullanıcıların veritabanınızdan veya uygulamanızda kullanılan diğer depolama
    | sistemlerinden nasıl alınacağını tanımlar. Genellikle Eloquent kullanılır.
    |
    | Desteklenen: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Kullanıcı Sağlayıcıları
    |--------------------------------------------------------------------------
    |
    | Tüm kimlik doğrulama guard'larının bir kullanıcı sağlayıcısı vardır, bu da
    | kullanıcıların veritabanınızdan veya uygulamanızda kullanılan diğer depolama
    | sistemlerinden nasıl alınacağını tanımlar. Genellikle Eloquent kullanılır.
    |
    | Birden fazla kullanıcı tablonuz veya modeliniz varsa, birden fazla sağlayıcı
    | yapılandırabilirsiniz. Bu sağlayıcılar, tanımladığınız ek kimlik doğrulama
    | guard'larına atanabilir.
    |
    | Desteklenen: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Şifre Sıfırlama
    |--------------------------------------------------------------------------
    |
    | Bu yapılandırma seçenekleri, Laravel'in şifre sıfırlama işlevinin davranışını
    | belirtir; token depolama için kullanılan tablo ve kullanıcıları almak için
    | çağrılan kullanıcı sağlayıcısı dahil.
    |
    | Son kullanma süresi, her sıfırlama token'ının geçerli sayılacağı dakika
    | sayısıdır. Bu güvenlik özelliği, token'ların kısa ömürlü olmasını sağlar.
    |
    | Throttle ayarı, bir kullanıcının yeni şifre sıfırlama token'ı oluşturmak için
    | beklemesi gereken saniye sayısıdır. Bu, çok sayıda token oluşturulmasını önler.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Şifre Onay Zaman Aşımı
    |--------------------------------------------------------------------------
    |
    | Burada, bir şifre onay penceresinin süresi dolmadan önce kaç saniye geçmesi
    | gerektiğini tanımlayabilirsiniz. Varsayılan olarak, zaman aşımı üç saattir.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
