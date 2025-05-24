<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TicketmasterService;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    protected $ticketmasterService;

    public function __construct(TicketmasterService $ticketmasterService)
    {
        $this->ticketmasterService = $ticketmasterService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Sadece yayınlanmış etkinlikleri ve tarihe göre sıralı getir
        $query = Event::published()->with('ticketTypes')->orderByDate();

        // Etkinlik türüne göre filtreleme
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Tarihe göre filtreleme
        if ($request->has('date')) {
            $query->whereDate('event_date', $request->date);
        }

        // Kullanıcının ilgi alanlarına göre öneriler
        if ($request->has('recommended') && Auth::check()) {
            $userInterests = Auth::user()->interests ?? [];
            $query->matchingInterests($userInterests);
        }

        $events = $query->paginate(10);

        // Her bir yerel etkinliğe price ve available_tickets ekleyelim (ticketTypes'dan)
        $events->getCollection()->transform(function ($event) {
            if ($event->relationLoaded('ticketTypes') && $event->ticketTypes->isNotEmpty()) {
                $firstTicketType = $event->ticketTypes->first();
                $event->price = $firstTicketType->price ?? 0; // İlk bilet türünün fiyatı, yoksa 0
                // Toplam kalan bilet sayısı için bir mantık eklenebilir (tüm ticket type'lardaki quantity toplamı)
                // Şimdilik available_tickets'ı ilk bilet türünün quantity'si olarak ayarlayabiliriz veya genel kapasiteyi kullanabiliriz.
                // Eğer event modelinde capacity alanı varsa onu kullanalım.
                $event->available_tickets = $event->capacity ?? $event->ticketTypes->sum('quantity'); 
            } else {
                $event->price = 0; // Bilet türü yoksa fiyat 0
                $event->available_tickets = $event->capacity ?? 0; // Bilet türü yoksa ve kapasite varsa onu, yoksa 0
            }
            return $event;
        });

        return response()->json([
            'status' => 'success',
            'data' => $events
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return response()->json([
            'status' => 'success',
            'data' => $event
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getEventTypes()
    {
        $types = [
            'concert' => 'Konser',
            'theatre' => 'Tiyatro',
            'sports' => 'Spor',
            'comedy' => 'Komedi'
        ];

        return response()->json([
            'status' => 'success',
            'data' => $types
        ]);
    }

    /**
     * Fetches events from Ticketmaster.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ticketmasterEvents(Request $request)
    {
        $city = $request->input('city', 'İstanbul'); // Şehir İstanbul olarak sabitlendi
        $countryCode = $request->input('countryCode', 'TR'); // Ülke kodu TR olarak sabitlendi

        $allApiEvents = $this->ticketmasterService->getEventsByCity($city, $countryCode);

        if ($allApiEvents === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticketmaster API key not configured or API request failed.'
            ], 500);
        }
        
        if (empty($allApiEvents)) {
            return response()->json([
                'status' => 'success',
                'message' => 'No events found from Ticketmaster for ' . $city . '.',
                'data' => []
            ]);
        }

        // Sadece yayınlanmış API etkinliklerini filtrele
        $processedEvents = collect($allApiEvents)->map(function ($event) {
            if (empty($event['id'])) {
                return null; // ID yoksa atla
            }
            $state = \App\Models\ApiEventState::where('event_id', $event['id'])->first();

            if (!$state || !$state->is_published) {
                return null; // Yayınlanmamışsa veya state yoksa atla
            }

            // Fiyatı belirle
            $displayPrice = null;
            $priceSource = 'api'; // Fiyatın kaynağını belirtmek için (api, override)

            if (isset($state->custom_data['override_price']) && $state->custom_data['override_price'] !== null) {
                $displayPrice = (float)$state->custom_data['override_price'];
                $priceSource = 'override';
            } elseif (isset($event['priceRanges']) && count($event['priceRanges']) > 0) {
                // API'den gelen fiyatı kullan (örn. min fiyat)
                $apiPrice = $event['priceRanges'][0]['min'] ?? null;
                if ($apiPrice !== null) {
                    $displayPrice = (float)$apiPrice;
                }
            }
            // Gerekirse burada bir varsayılan fiyat da atanabilir (örn: 100)
            // if ($displayPrice === null) $displayPrice = 100.00; 

            // Orijinal event verisine display_price ve price_source ekleyerek döndür
            $event['display_price'] = $displayPrice;
            $event['price_source'] = $priceSource;
            
            $currentCustomData = $state->custom_data ?? [];
            $event['custom_data'] = $currentCustomData;

            return $event;
        })->filter()->values(); // null olanları kaldır ve yeniden indeksle

        return response()->json([
            'status' => 'success',
            'data' => $processedEvents
        ]);
    }
}
