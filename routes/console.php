//Bu kod, terminalde php artisan inspire komutunu çalıştırdığında sana rastgele bir ilham verici söz gösterir.
// Laravel’in örnek ve öğretici amaçlı bıraktığı bir komuttur.
// Başka bir Artisan komutu veya Laravel özelliği hakkında da bilgi almak istersen, bana sorabilirsin!
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// İlham verici bir sözü ekrana yazdıran özel Artisan komutu
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('İlham verici bir sözü göster');
