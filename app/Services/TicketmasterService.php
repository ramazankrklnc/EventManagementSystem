<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TicketmasterService
{
    // Ticketmaster API anahtarı
    protected $apiKey;
    // Ticketmaster API temel URL'si
    protected $baseUrl;

    // Sınıf başlatıldığında API anahtarı ve base URL'yi ayarla
    public function __construct()
    {
        $this->apiKey = config('ticketmaster.api_key');
        $this->baseUrl = config('ticketmaster.base_url');
    }

    /**
     * Belirli bir şehirdeki etkinlikleri Ticketmaster API'sinden getirir.
     *
     * @param string $city Şehir adı (örneğin, 'İstanbul')
     * @param string $countryCode Ülke kodu (örneğin, 'TR')
     * @param array $additionalParams API isteği için ek parametreler
     * @return array|null API'den gelen yanıt veya hata durumunda null
     */
    public function getEventsByCity(string $city, string $countryCode = 'TR', array $additionalParams = []): ?array
    {
        // API anahtarı yoksa null döndür
        if (!$this->apiKey) {
            return null;
        }

        // API'ye gönderilecek parametreleri hazırla
        $queryParams = array_merge([
            'apikey' => $this->apiKey,
            'city' => $city,
            'countryCode' => $countryCode,
            'size' => 20, // Maksimum 20 etkinlik getir
            'sort' => 'date,asc' // Tarihe göre sırala
        ], $additionalParams);

        // Ticketmaster API endpoint'i
        $fullUrl = $this->baseUrl . 'events.json';

        // API'ye GET isteği gönder
        $response = Http::timeout(15)->get($fullUrl, $queryParams);

        // Başarılıysa etkinlikleri döndür, değilse null döndür
        if ($response->successful()) {
            $events = $response->json();
            // Ticketmaster genellikle etkinlikleri '_embedded.events' altında döndürür
            return $events['_embedded']['events'] ?? [];
        } elseif ($response->status() == 401) {
            // Yetkisiz erişim (API anahtarı hatalı)
            return null;
        } elseif ($response->status() == 403) {
            // Erişim engellendi (API limiti veya izin yok)
            return null;
        } else {
            // Diğer hata durumları
            return null;
        }
    }

    /**
     * Belirli bir ID'ye sahip etkinliği Ticketmaster API'sinden getirir.
     *
     * @param string $eventId Etkinlik ID'si
     * @return array|null API'den gelen yanıt veya hata durumunda null
     */
    public function getEventById(string $eventId): ?array
    {
        // API anahtarı yoksa null döndür
        if (!$this->apiKey) {
            return null;
        }

        // API'ye gönderilecek parametreler
        $queryParams = [
            'apikey' => $this->apiKey
        ];

        // Ticketmaster API event detay endpoint'i
        $fullUrl = $this->baseUrl . 'events/' . $eventId;

        // API'ye GET isteği gönder
        $response = Http::timeout(15)->get($fullUrl, $queryParams);

        // Başarılıysa etkinlik detayını döndür, değilse null döndür
        if ($response->successful()) {
            $event = $response->json();
            return $event;
        } else {
            return null;
        }
    }
} 