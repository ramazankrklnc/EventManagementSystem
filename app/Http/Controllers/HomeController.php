<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Http\Controllers\Admin\AnnouncementController;
use Illuminate\Support\Facades\Route;

/**
 * Ana Sayfa Controller'ı
 * 
 * Bu controller, uygulamanın ana sayfasını yönetir.
 * Kullanıcıların giriş yapmadan önce ve sonra göreceği
 * etkinlikleri ve duyuruları listeler.
 */
class HomeController extends Controller
{
    /**
     * Ana sayfayı gösterir
     * 
     * Bu metod, kullanıcıların ana sayfada göreceği içeriği
     * hazırlar ve görüntüler. İleride burada:
     * - Öne çıkan etkinlikler
     * - Son duyurular
     * - Hava durumu bilgisi
     * - Kullanıcıya özel etkinlik önerileri
     * gibi veriler eklenebilir.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Tarih sırasına göre etkinlikleri getir
        $events = Event::where('event_date', '>', now())
                      ->orderBy('event_date', 'asc')
                      ->get();

        // Hava durumu bilgisini al
        $weather = null;
        try {
            $apiKey = config('services.weather.api_key');
            $city = 'İstanbul';

            $response = Http::get("https://api.openweathermap.org/data/2.5/forecast", [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric',
                'lang' => 'tr'
            ]);

            \Log::info('Weather API Response:', [
                'status' => $response->status(),
                'body' => $response->body(),
                'api_key' => $apiKey
            ]);

            if ($response->successful()) {
                $weather = $response->json();
            }
        } catch (\Exception $e) {
            \Log::error('Hava durumu bilgisi alınamadı: ' . $e->getMessage());
        }

        // Önerilen etkinlikleri başlangıçta boş bir koleksiyon olarak tanımla
        $recommendedEvents = collect();

        // Kullanıcı giriş yapmışsa, ilgi alanlarına göre önerileri getir
        if (auth()->check()) {
            $userInterests = auth()->user()->interests ?? [];
            if (!empty($userInterests)) {
                $recommendedEvents = Event::whereIn('event_type', $userInterests)
                                        ->where('event_date', '>', now())
                                        ->orderBy('event_date', 'asc')
                                        ->take(3)
                                        ->get();
            }
        }

        // Hava durumuna bağlı etkinlikleri kontrol et
        $weatherDependentEvents = Event::where('weather_dependent', true)
                                     ->where('event_date', '>', now())
                                     ->where('event_date', '<', now()->addDays(7))
                                     ->get()
                                     ->map(function ($event) use ($weather) {
                                         $event->weather_status = $this->checkWeatherForEvent($event, $weather);
                                         return $event;
                                     });

        $announcements = Announcement::latest()->take(3)->get();

        return view('home', compact('events', 'announcements', 'weather', 'recommendedEvents', 'weatherDependentEvents'));
    }

    private function checkWeatherForEvent($event, $weather)
    {
        if (!$weather) {
            return [
                'status' => 'unknown',
                'message' => 'Hava durumu bilgisi alınamadı'
            ];
        }

        // Etkinlik tarihine en yakın hava durumu tahminini bul
        $eventDate = Carbon::parse($event->event_date);
        $forecast = collect($weather['list'])->first(function ($item) use ($eventDate) {
            return Carbon::parse($item['dt'])->isSameDay($eventDate);
        });

        if (!$forecast) {
            return [
                'status' => 'unknown',
                'message' => 'Bu tarih için hava durumu tahmini yok'
            ];
        }

        // Hava durumuna göre etkinlik durumunu belirle
        $weatherCode = $forecast['weather'][0]['id'];
        $temperature = $forecast['main']['temp'];

        // Hava durumu kodları: https://openweathermap.org/weather-conditions
        if ($weatherCode >= 200 && $weatherCode < 600) {
            return [
                'status' => 'risky',
                'message' => 'Etkinlik yağışlı hava nedeniyle riskli olabilir',
                'temperature' => $temperature,
                'condition' => $forecast['weather'][0]['description']
            ];
        } elseif ($temperature > 30 || $temperature < 5) {
            return [
                'status' => 'risky',
                'message' => 'Sıcaklık etkinlik için uygun olmayabilir',
                'temperature' => $temperature,
                'condition' => $forecast['weather'][0]['description']
            ];
        }

        return [
            'status' => 'good',
            'message' => 'Hava durumu etkinlik için uygun',
            'temperature' => $temperature,
            'condition' => $forecast['weather'][0]['description']
        ];
    }

    public function home()
    {
        $events = \App\Models\Event::where('event_date', '>', now())
            ->orderBy('event_date', 'asc')
            ->get();
        $announcements = \App\Models\Announcement::where('status', 'active')->latest()->take(4)->get();
        $weather = null;
        try {
            $apiKey = env('WEATHER_API_KEY');
            $city = 'İstanbul';
            $response = \Illuminate\Support\Facades\Http::get('https://api.openweathermap.org/data/2.5/forecast', [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric',
                'lang' => 'tr'
            ]);
            if ($response->successful()) {
                $weather = $response->json();
                \Illuminate\Support\Facades\Log::info('Full Weather API Data for Home:', [$weather]);
            }
        } catch (\Exception $e) {
            $weather = null;
            \Illuminate\Support\Facades\Log::error('Error fetching weather data in home method: ' . $e->getMessage());
        }

        return view('home', compact('weather', 'announcements', 'events'));
    }

    public function indexNewDesign()
    {
        // Örnek veri çekme, kendi modelinize ve ihtiyacınıza göre uyarlayın
        $announcements = \App\Models\Announcement::latest()->paginate(10); 
        return view('admin.announcements.index_new_design', compact('announcements'));
    }
} 