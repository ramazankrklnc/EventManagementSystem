<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\ApiEventState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\TicketmasterService;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    protected $ticketmasterService;

    public function __construct(TicketmasterService $ticketmasterService)
    {
        // Sadece admin kullanıcıların erişimine izin ver
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                abort(403, 'Yetkiniz yok');
            }
            return $next($request);
        });

        $this->ticketmasterService = $ticketmasterService;
    }

    /**
     * Tüm etkinlikleri listele
     * Admin panelinde etkinliklerin listesini döndürür
     */
    public function index()
    {
        try {
            // 1. Yerel Etkinlikleri Çek (Bilet Türleri ile birlikte)
            $localEvents = Event::with('ticketTypes')->orderBy('event_date', 'desc')->get()->map(function ($event) {
                $event->is_api_event = false;
                $event->source = 'local';
                // Yerel etkinlikler için fiyat ve kontenjan gösterimi için mantık eklenecek
                return $event;
            });

            // 2. Ticketmaster'dan etkinlikleri çek
            $apiEventsRaw = $this->ticketmasterService->getEventsByCity('İstanbul', 'TR');
            if (!is_array($apiEventsRaw)) {
                $apiEventsRaw = [];
            }

            $apiEventsFormatted = collect($apiEventsRaw)->map(function ($eventData) {
                $eventId = $eventData['id'] ?? null;
                if (!$eventId) return null; // ID yoksa atla

                // Her API etkinliği için ApiEventState kaydı oluştur veya güncelle
                $state = ApiEventState::firstOrCreate(
                    ['event_id' => $eventId],
                    [
                        'is_published' => false,
                        'custom_data' => [
                            'override_capacity' => 100, // Varsayılan kontenjan
                            'override_price' => null
                        ]
                    ]
                );

                \Log::info('API Event Listeleme - ApiEventState durumu:', [
                    'event_id' => $eventId,
                    'event_name' => $eventData['name'] ?? 'İsimsiz',
                    'api_event_state_id' => $state->id,
                    'custom_data' => $state->custom_data,
                    'is_published' => $state->is_published
                ]);
                
                // Fiyat bilgisi (Ticketmaster API yanıtından alınmaya çalışılabilir, yoksa null)
                $priceInfo = null;
                if (isset($eventData['priceRanges']) && count($eventData['priceRanges']) > 0) {
                    $priceRange = $eventData['priceRanges'][0];
                    $minPrice = $priceRange['min'] ?? null;
                    $maxPrice = $priceRange['max'] ?? null;
                    $currency = $priceRange['currency'] ?? '';
                    if ($minPrice && $maxPrice) {
                        $priceInfo = "{$minPrice} - {$maxPrice} {$currency}";
                    } elseif ($minPrice) {
                        $priceInfo = "{$minPrice} {$currency}";
                    }
                }

                return (object) [
                    'id' => $eventId,
                    'title' => $eventData['name'] ?? 'Başlık Yok',
                    'event_date' => isset($eventData['dates']['start']['dateTime']) 
                        ? \Carbon\Carbon::parse($eventData['dates']['start']['dateTime'])
                        : null,
                    'location' => $eventData['_embedded']['venues'][0]['name'] ?? 'Konum Yok',
                    'type' => $eventData['classifications'][0]['segment']['name'] ?? 'Tür Yok',
                    'is_published' => $state->is_published,
                    'source' => 'ticketmaster',
                    'is_api_event' => true,
                    'price_info_api' => $priceInfo, // API'dan gelen fiyat bilgisi (orijinal)
                    'capacity_info_api' => 'API', // API için kapasite (orijinal)
                    'custom_data' => $state->custom_data // Özel veriyi de ekle
                ];
            })->filter(); // Null olanları kaldır

            // İki koleksiyonu birleştir
            $allEvents = $localEvents->concat($apiEventsFormatted);
            
            // Tarihe göre sırala (en yeniden en eskiye)
            $sortedEvents = $allEvents->sortByDesc(function ($event) {
                return $event->event_date ? $event->event_date->timestamp : 0;
            });

            return view('admin.events.index', [
                'events' => $sortedEvents,
                'isApiEvents' => false, // Artık karma bir liste olduğu için bu flag'i kaldırabiliriz veya amacını değiştirebiliriz
                'hasPages' => false 
            ]);

        } catch (\Exception $e) {
            // Hata durumunda sadece yerel etkinlikleri göstermeyi veya boş bir koleksiyon göndermeyi düşünebiliriz.
            $localEvents = Event::with('ticketTypes')->orderBy('event_date', 'desc')->get()->map(function ($event) {
                $event->is_api_event = false;
                $event->source = 'local';
                return $event;
            });
            
            return view('admin.events.index', [
                'events' => $localEvents, // Hata durumunda sadece yerel etkinlikler
                'error' => 'API\'dan etkinlikler çekilirken bir hata oluştu (yerel etkinlikler gösteriliyor): ' . $e->getMessage(),
                'hasPages' => false
            ]);
        }
    }

    /**
     * Yeni etkinlik oluşturma formunu göster.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Yeni oluşturulan etkinliği veritabanında sakla.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|max:100',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'categories_input' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'weather_dependent' => 'sometimes|boolean',
            'is_published' => 'sometimes|boolean',
            'ticket_types' => 'nullable|array',
            'ticket_types.*.name' => 'required_with:ticket_types|string|max:255',
            'ticket_types.*.price' => 'required_with:ticket_types|numeric|min:0',
            'ticket_types.*.quantity' => 'required_with:ticket_types|integer|min:0',
        ]);

        $validatedData['weather_dependent'] = $request->has('weather_dependent');
        $validatedData['is_published'] = $request->has('is_published');
        
        if (!empty($validatedData['categories_input'])) {
            $categoriesArray = array_map('trim', explode(',', $validatedData['categories_input']));
            $validatedData['categories'] = json_encode(array_filter($categoriesArray));
        } else {
            $validatedData['categories'] = json_encode([]);
        }
        unset($validatedData['categories_input']);

        // Etkinlik ana verilerini ayır
        $eventMainData = collect($validatedData)->except('ticket_types')->toArray();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/events');
            $eventMainData['image_url'] = Storage::url($path);
        }

        $event = Event::create($eventMainData);

        // Bilet türlerini işle
        if ($request->has('ticket_types') && is_array($request->ticket_types)) {
            foreach ($request->ticket_types as $ticketTypeData) {
                if (!empty($ticketTypeData['name']) && isset($ticketTypeData['price']) && isset($ticketTypeData['quantity'])) {
                    $event->ticketTypes()->create([
                        'name' => $ticketTypeData['name'],
                        'price' => $ticketTypeData['price'],
                        'quantity' => $ticketTypeData['quantity'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.events.index')->with('success', 'Etkinlik başarıyla oluşturuldu.');
    }

    /**
     * Belirtilen etkinliği düzenleme formunu göster.
     *
     * @param \App\Models\Event $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        if (is_string($event->categories)) {
            $event->categories = json_decode($event->categories, true);
        }
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Veritabanındaki belirtilen etkinliği güncelle.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Event $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|max:100',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'categories_input' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'weather_dependent' => 'sometimes|boolean',
            'is_published' => 'sometimes|boolean',
            'ticket_types' => 'nullable|array',
            'ticket_types.*.id' => 'nullable|integer|exists:ticket_types,id',
            'ticket_types.*.name' => 'required_with:ticket_types|string|max:255',
            'ticket_types.*.price' => 'required_with:ticket_types|numeric|min:0',
            'ticket_types.*.quantity' => 'required_with:ticket_types|integer|min:0',
        ]);

        $validatedData['weather_dependent'] = $request->has('weather_dependent');
        $validatedData['is_published'] = $request->has('is_published');

        if (!empty($validatedData['categories_input'])) {
            $categoriesArray = array_map('trim', explode(',', $validatedData['categories_input']));
            $validatedData['categories'] = json_encode(array_filter($categoriesArray));
        } else {
            $validatedData['categories'] = json_encode([]);
        }
        unset($validatedData['categories_input']);
        
        $eventMainData = collect($validatedData)->except('ticket_types')->toArray();

        if ($request->hasFile('image')) {
            if ($event->image_url) {
                $oldImagePath = str_replace(Storage::url(''), 'public/', $event->image_url);
                if (Storage::exists($oldImagePath)) {
                    Storage::delete($oldImagePath);
                }
            }
            $path = $request->file('image')->store('public/events');
            $eventMainData['image_url'] = Storage::url($path);
        }

        $event->update($eventMainData);

        // Bilet türlerini işle (güncelle, ekle, sil)
        $submittedTicketTypeIds = [];
        if ($request->has('ticket_types') && is_array($request->ticket_types)) {
            foreach ($request->ticket_types as $ticketTypeData) {
                if (!empty($ticketTypeData['name']) && isset($ticketTypeData['price']) && isset($ticketTypeData['quantity'])) {
                    $dataToUpdateOrCreate = [
                        'name' => $ticketTypeData['name'],
                        'price' => $ticketTypeData['price'],
                        'quantity' => $ticketTypeData['quantity'],
                    ];

                    if (!empty($ticketTypeData['id'])) {
                        // Mevcut bilet türünü güncelle
                        $ticketType = $event->ticketTypes()->find($ticketTypeData['id']);
                        if ($ticketType) {
                            $ticketType->update($dataToUpdateOrCreate);
                            $submittedTicketTypeIds[] = $ticketType->id;
                        }
                    } else {
                        // Yeni bilet türü oluştur
                        $newTicketType = $event->ticketTypes()->create($dataToUpdateOrCreate);
                        $submittedTicketTypeIds[] = $newTicketType->id;
                    }
                }
            }
        }
        // Formdan gelmeyen (silinmiş) bilet türlerini veritabanından sil
        $event->ticketTypes()->whereNotIn('id', $submittedTicketTypeIds)->delete();

        return redirect()->route('admin.events.index')->with('success', 'Etkinlik başarıyla güncellendi.');
    }

    /**
     * Belirtilen etkinliği kaldır (soft delete).
     *
     * @param \App\Models\Event $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Etkinlik başarıyla silindi.');
    }

    /**
     * Etkinliğin yayınlanma durumunu değiştir.
     *
     * @param \App\Models\Event $event
     * @return \Illuminate\Http\Response
     */
    public function togglePublish(Event $event)
    {
        $event->is_published = !$event->is_published;
        $event->save();

        $message = $event->is_published ? 'Etkinlik başarıyla yayınlandı.' : 'Etkinlik başarıyla yayından kaldırıldı.';
        return back()->with('success', $message);
    }

    /**
     * API'dan gelen etkinliğin yayın durumunu değiştir
     */
    public function toggleApiEventPublish($eventId)
    {
        $apiEventState = ApiEventState::where('event_id', $eventId)->first();

        if ($apiEventState) {
            $apiEventState->is_published = !$apiEventState->is_published;
            $apiEventState->save();
            return redirect()->route('admin.events.index')->with('success', 'API etkinliğinin yayın durumu güncellendi.');
        }

        // Eğer state yoksa, oluştur ve taslak olarak ayarla (ya da yayınla, duruma göre)
        // Bu senaryo normalde index'te firstOrCreate ile oluştuğu için pek olası değil ama defensive kodlama.
        $apiEventState = ApiEventState::create([
            'event_id' => $eventId,
            'is_published' => true, // Yeni oluşturulan hemen yayınlansın mı? Veya false mu olmalı? Şimdilik true.
            'custom_data' => []
        ]);
        return redirect()->route('admin.events.index')->with('success', 'API etkinliği oluşturuldu ve yayın durumu güncellendi.');
    }

    /**
     * API'dan gelen etkinliği düzenleme formunu göster
     */
    public function editApiEvent($eventId)
    {
        $apiEventState = ApiEventState::firstOrCreate(
            ['event_id' => $eventId],
            ['is_published' => false, 'custom_data' => []] // custom_data default boş array
        );
    
        // custom_data'nın null değil, array olduğundan emin ol
        if (is_null($apiEventState->custom_data)) {
            $apiEventState->custom_data = [];
        }

        $eventDetails = $this->ticketmasterService->getEventById($eventId);

        if (!$eventDetails) {
            return redirect()->route('admin.events.index')->with('error', 'Ticketmaster etkinliği detayları alınamadı (ID: ' . $eventId . '). Servis yanıt vermiyor veya etkinlik bulunamadı.');
        }
        
        $priceInfoApi = null;
        if (isset($eventDetails['priceRanges']) && count($eventDetails['priceRanges']) > 0) {
            $priceRange = $eventDetails['priceRanges'][0];
            $minPrice = $priceRange['min'] ?? null;
            $maxPrice = $priceRange['max'] ?? null;
            $currency = $priceRange['currency'] ?? '';
            if ($minPrice && $maxPrice) {
                $priceInfoApi = "{$minPrice} - {$maxPrice} {$currency}";
            } elseif ($minPrice) {
                $priceInfoApi = "{$minPrice} {$currency}";
            }
        }

        return view('admin.events.edit_api', [
            'apiEventState' => $apiEventState,
            'eventDetails' => $eventDetails,
            'priceInfoApi' => $priceInfoApi
        ]);
    }

    /**
     * API'dan gelen etkinliği güncelle
     */
    public function updateApiEvent(Request $request, $eventId)
    {
        try {
            $request->validate([
                'custom_data.override_price' => 'nullable|numeric|min:0',
                'custom_data.override_capacity' => 'required|integer|min:1',
                'is_published' => 'sometimes|boolean',
            ]);

            $apiEventState = ApiEventState::firstOrCreate(
                ['event_id' => $eventId],
                ['is_published' => false, 'custom_data' => []]
            );

            \Log::info('API Event Güncelleme - Başlangıç:', [
                'event_id' => $eventId,
                'mevcut_custom_data' => $apiEventState->custom_data,
                'gelen_custom_data' => $request->input('custom_data')
            ]);

            $customData = $apiEventState->custom_data ?? [];
            $submittedCustomData = $request->input('custom_data', []); // Default olarak boş array
            
            \Log::info('API Event Güncelleme - submittedCustomData:', $submittedCustomData);
            \Log::info('API Event Güncelleme - customData (önce):', $customData);

            if (array_key_exists('override_price', $submittedCustomData) && $submittedCustomData['override_price'] !== null && $submittedCustomData['override_price'] !== '') {
                $customData['override_price'] = (float)$submittedCustomData['override_price'];
                \Log::info('API Event Güncelleme - override_price güncellendi:', ['yeni_değer' => $customData['override_price']]);
            } else {
                unset($customData['override_price']);
                \Log::info('API Event Güncelleme - override_price kaldırıldı');
            }

            if (array_key_exists('override_capacity', $submittedCustomData) && $submittedCustomData['override_capacity'] !== null && $submittedCustomData['override_capacity'] !== '') {
                $customData['override_capacity'] = (int)$submittedCustomData['override_capacity'];
            }
            
            // Çocuk Bileti Fiyatı
            if (array_key_exists('child_price', $submittedCustomData) && $submittedCustomData['child_price'] !== null && $submittedCustomData['child_price'] !== '') {
                $customData['child_price'] = (float)$submittedCustomData['child_price'];
            } else {
                unset($customData['child_price']);
            }
            // Tam Bilet Fiyatı
            if (array_key_exists('adult_price', $submittedCustomData) && $submittedCustomData['adult_price'] !== null && $submittedCustomData['adult_price'] !== '') {
                $customData['adult_price'] = (float)$submittedCustomData['adult_price'];
            } else {
                unset($customData['adult_price']);
            }
            
            \Log::info('API Event Güncelleme - customData (sonra):', $customData);

            $apiEventState->custom_data = $customData;
            $apiEventState->is_published = $request->boolean('is_published');
            
            $apiEventState->save();

            \Log::info('API Event Güncelleme - ApiEventState (kayıt sonrası):', ['event_id' => $eventId, 'custom_data' => $apiEventState->fresh()->custom_data]);

            return redirect()->route('admin.events.index')->with('success', 'API etkinliği başarıyla güncellendi.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validasyon hatalarını logla ve kullanıcıya geri bildirim yap
            Log::warning("updateApiEvent - Validation Error for eventId: {$eventId}: " . $e->getMessage(), ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) { // Diğer genel hatalar
            Log::error("updateApiEvent - Exception for eventId: {$eventId}: " . $e->getMessage(), [
                'exception_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withInput()->with('error', 'Etkinlik güncellenirken beklenmedik bir hata oluştu.');
        }
    }
}
