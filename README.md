# Etkinlik Yönetim Sistemi (EYS)

Etkinlik Yönetim Sistemi, çeşitli etkinliklerin yönetimi, duyurulması ve bilet satışı için geliştirilmiş modern bir web uygulamasıdır.

## Özellikler

### Kullanıcı Yönetimi
- İki tip kullanıcı: Normal kullanıcı ve yönetici
- Kayıt olan kullanıcılar için yönetici onayı sistemi
- İlk girişte şifre değiştirme zorunluluğu
- Güvenli giriş ve yetkilendirme

### Etkinlik Yönetimi
- Farklı türlerde etkinliklerin yayınlanması (konser, sergi, tiyatro, spor vb.)
- Etkinlikler için tarih, konum, fiyat ve kontenjan bilgileri
- Etkinliklerin yayınlanması veya yayından kaldırılması
- Hava durumuna bağlı etkinlik planlaması
- Dış kaynaklı etkinlik entegrasyonu (özelleştirilebilir API)

### Bilet Sistemi
- Sepet yapısı ile bilet seçimi
- Farklı bilet türleri ve fiyatlandırma
- Bilet satın alma işlemi ve kontenjan takibi
- Ödeme yöntemi seçimi

### Duyuru Sistemi
- Yöneticiler tarafından oluşturulan duyurular
- Aktif ve pasif duyuru yönetimi

### API Entegrasyonları
- Dış etkinlik kaynağı entegrasyonu (varsayılan olarak Ticketmaster API)
- Hava durumu entegrasyonu (varsayılan olarak OpenWeatherMap API)
- Genişletilebilir API servis yapısı

## Teknolojiler

- PHP 8.1+
- Laravel 10.x
- MySQL / MariaDB
- Vue.js
- Tailwind CSS
- JavaScript / ES6
- HTML5 / CSS3

## Kurulum

### Gereksinimler
- PHP 8.1 veya daha yüksek
- Composer
- Node.js ve NPM
- MySQL veya MariaDB veritabanı

### Adımlar

1. Projeyi klonlayın:
```bash
git clone https://github.com/ramazankrklnc/EventManagementSystem.git
cd EventManagementSystem
```

2. Bağımlılıkları yükleyin:
```bash
composer install
npm install
```

3. `.env` dosyasını oluşturun:
```bash
cp .env.example .env
```

4. `.env` dosyasını düzenleyerek veritabanı ve API ayarlarını yapın:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eys_db
DB_USERNAME=root
DB_PASSWORD=

# Etkinlik API Ayarları - Varsayılan: Ticketmaster
EVENT_API_PROVIDER=ticketmaster
TICKETMASTER_API_KEY=YOUR_API_KEY
TICKETMASTER_BASE_URL=https://app.ticketmaster.com/discovery/v2/

# Hava Durumu API Ayarları - Varsayılan: OpenWeatherMap
WEATHER_API_PROVIDER=openweathermap
WEATHER_API_KEY=YOUR_API_KEY
WEATHER_API_BASE_URL=https://api.openweathermap.org/data/2.5/
```

5. Uygulama anahtarını oluşturun:
```bash
php artisan key:generate
```

6. Veritabanını oluşturun ve migrationları çalıştırın:
```bash
php artisan migrate
```

7. (İsteğe bağlı) Örnek veriler ekleyin:
```bash
php artisan db:seed
```

8. Varlıkları derleyin:
```bash
npm run dev
```

9. Uygulamayı başlatın:
```bash
php artisan serve
```

10. Tarayıcınızda `http://localhost:8000` adresine giderek uygulamayı kullanmaya başlayabilirsiniz.

## Kullanım

### Yönetici Hesabı
Varsayılan olarak bir yönetici hesabı oluşturulur:
- E-posta: admin@example.com
- Şifre: password

İlk girişte şifrenizi değiştirmeniz istenecektir.

### Kullanıcı Kaydı
1. Kayıt ol sayfasından yeni hesap oluşturun
2. Yönetici onayını bekleyin
3. Onaylanan hesapla giriş yapın ve şifrenizi değiştirin
4. Etkinlikleri görüntüleyin ve bilet alın

### Yönetici İşlemleri
1. Kullanıcı onayları
2. Etkinlik ekleme, düzenleme, silme
3. Duyuru yönetimi
4. API etkinliklerini yayınlama

## API Entegrasyonlarını Özelleştirme

Sistem, farklı API sağlayıcılarıyla çalışacak şekilde tasarlanmıştır. Varsayılan olarak Ticketmaster (etkinlikler) ve OpenWeatherMap (hava durumu) kullanılmaktadır, ancak kendi API entegrasyonlarınızı ekleyebilirsiniz.

### Yeni Etkinlik API Sağlayıcısı Ekleme

1. `app/Services` dizininde yeni bir servis sınıfı oluşturun:

```php
<?php

namespace App\Services;

class YourCustomEventService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.your_custom_service.api_key');
        $this->baseUrl = config('services.your_custom_service.base_url');
    }

    public function getEventsByCity(string $city, string $countryCode = 'TR', array $additionalParams = []): ?array
    {
        // API'den etkinlikleri çekme mantığınızı buraya uygulayın
        // ...

        return $events;
    }

    public function getEventById(string $eventId): ?array
    {
        // API'den belirli bir etkinliği çekme mantığınızı buraya uygulayın
        // ...

        return $eventDetails;
    }
}
```

2. `config/services.php` dosyasını güncelleyin:

```php
'your_custom_service' => [
    'api_key' => env('YOUR_CUSTOM_SERVICE_API_KEY'),
    'base_url' => env('YOUR_CUSTOM_SERVICE_BASE_URL'),
],
```

3. `.env` dosyasında ayarları tanımlayın:

```
EVENT_API_PROVIDER=your_custom_service
YOUR_CUSTOM_SERVICE_API_KEY=your_api_key
YOUR_CUSTOM_SERVICE_BASE_URL=https://api.example.com/
```

4. `AppServiceProvider.php` içinde servis sağlayıcısını kaydedin:

```php
$this->app->bind('App\Services\EventServiceInterface', function ($app) {
    $provider = config('services.event_api_provider', 'ticketmaster');
    
    return match($provider) {
        'your_custom_service' => new YourCustomEventService(),
        'ticketmaster' => new TicketmasterService(),
        default => new TicketmasterService(),
    };
});
```

### Yeni Hava Durumu API Sağlayıcısı Ekleme

Benzer şekilde, hava durumu API sağlayıcınızı da özelleştirebilirsiniz. `app/Services` dizininde yeni bir hava durumu servisi oluşturun ve ilgili konfigürasyonu yapın.

## Lisans

Bu proje [MIT lisansı](LICENSE) altında lisanslanmıştır.
