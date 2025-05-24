<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Varsayılan Mailer
    |--------------------------------------------------------------------------
    |
    | Bu seçenek, başka bir mailer açıkça belirtilmediği sürece tüm e-posta
    | mesajlarını göndermek için kullanılan varsayılan mailer'ı kontrol eder.
    | Tüm ek mailer'lar "mailers" dizisinde yapılandırılabilir.
    |
    */

    'default' => env('MAIL_MAILER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Yapılandırmaları
    |--------------------------------------------------------------------------
    |
    | Uygulamanızda kullanılan tüm mailer'ları ve ilgili ayarlarını burada
    | yapılandırabilirsiniz. Laravel, e-posta gönderimi için çeşitli "transport"
    | sürücülerini destekler. Aşağıda örnekler verilmiştir.
    |
    | Desteklenen: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
    |              "postmark", "resend", "log", "array",
    |              "failover", "roundrobin"
    |
    */

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
            // 'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID'),
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'resend' => [
            'transport' => 'resend',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
            'retry_after' => 60,
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
            'retry_after' => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Genel "Kimden" Adresi
    |--------------------------------------------------------------------------
    |
    | Uygulamanız tarafından gönderilen tüm e-postaların aynı adresten
    | gönderilmesini isteyebilirsiniz. Burada, uygulamanız tarafından
    | gönderilen tüm e-postalar için kullanılacak ad ve adresi belirtebilirsiniz.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

];
