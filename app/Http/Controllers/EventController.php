<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

/**
 * Event Controller
 * 
 * Bu controller etkinliklerin CRUD (Create, Read, Update, Delete) işlemlerini yönetir.
 * Etkinlik oluşturma, düzenleme ve silme işlemleri için kullanıcı girişi gereklidir.
 * Etkinlik listeleme ve detay görüntüleme herkese açıktır.
 */
class EventController extends Controller
{
    /**
     * Event Controller
     * 
     * Bu controller etkinliklerin CRUD (Create, Read, Update, Delete) işlemlerini yönetir.
     * Etkinlik oluşturma, düzenleme ve silme işlemleri için kullanıcı girişi gereklidir.
     * Etkinlik listeleme ve detay görüntüleme herkese açıktır.
     */
    public function __construct()
    {
        // Middleware artık route dosyasında tanımlı
    }

    /**
     * Etkinlikleri listele
     * 
     * Aktif ve gelecek tarihli etkinlikleri sayfalı olarak listeler.
     * Her sayfada 12 etkinlik gösterilir.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $events = Event::with('user')
            ->where('status', 'active')
            ->where('event_date', '>', now())
            ->orderBy('event_date')
            ->paginate(12);

        return view('events.index', compact('events'));
    }

    /**
     * Etkinlik detayını göster
     * 
     * @param Event $event
     * @return \Illuminate\View\View
     */
    public function show(Event $event)
    {
        // Hava durumu bilgisini al
        $weather = $this->getWeatherData();
        
        // Eğer etkinlik hava durumuna bağlıysa, hava durumu durumunu kontrol et
        if ($event->weather_dependent) {
            $event->weather_status = $this->checkWeatherForEvent($event, $weather);
        }

        return view('events.show', compact('event', 'weather'));
    }

    private function getWeatherData()
    {
        
        $apiKey = config('services.weather.api_key');
        $city = 'Istanbul'; // Varsayılan şehir

        try {
            $response = Http::get("https://api.openweathermap.org/data/2.5/forecast", [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric',
                'lang' => 'tr'
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            \Log::error('Hava durumu API hatası: ' . $e->getMessage());
        }

        return null;
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
        $eventDate = \Carbon\Carbon::parse($event->event_date);
        $forecast = collect($weather['list'])->first(function ($item) use ($eventDate) {
            return \Carbon\Carbon::parse($item['dt'])->isSameDay($eventDate);
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

    /**
     * Etkinlik oluşturma formunu göster
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Yeni etkinliği kaydet
     * 
     * Form verilerini doğrular, görsel yükleme işlemini yapar
     * ve yeni etkinliği veritabanına kaydeder.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Form verilerini doğrula
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'ticket_price' => 'required|numeric|min:0',
            'total_tickets' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048', // max 2MB
        ]);

        // Görsel yükleme işlemi
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            $validated['image_path'] = $path;
        }

        // Etkinliği oluşturan kullanıcıyı ekle ve bilet sayısını ayarla
        $validated['user_id'] = Auth::id();
        $validated['available_tickets'] = $validated['total_tickets'];

        $event = Event::create($validated);

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Etkinlik başarıyla oluşturuldu.');
    }

    /**
     * Etkinlik düzenleme formunu göster
     * 
     * Sadece etkinliği oluşturan kullanıcı düzenleyebilir.
     * 
     * @param Event $event
     * @return \Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Event $event)
    {
        // Yetki kontrolü
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        return view('events.edit', compact('event'));
    }

    /**
     * Etkinliği güncelle
     * 
     * Form verilerini doğrular, görsel güncelleme işlemini yapar
     * ve etkinliği veritabanında günceller.
     * 
     * @param Request $request
     * @param Event $event
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Event $event)
    {
        // Yetki kontrolü
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        // Form verilerini doğrula
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'ticket_price' => 'required|numeric|min:0',
            'total_tickets' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:active,cancelled,completed',
        ]);

        // Görsel güncelleme işlemi
        if ($request->hasFile('image')) {
            // Eski görseli sil
            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }
            $path = $request->file('image')->store('events', 'public');
            $validated['image_path'] = $path;
        }

        $event->update($validated);

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Etkinlik başarıyla güncellendi.');
    }

    /**
     * Etkinliği sil
     * 
     * Etkinliği ve ilişkili görseli siler.
     * Sadece etkinliği oluşturan kullanıcı silebilir.
     * 
     * @param Event $event
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Event $event)
    {
        // Yetki kontrolü
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        // Etkinlik görselini sil
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }

        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('success', 'Etkinlik başarıyla silindi.');
    }
}
