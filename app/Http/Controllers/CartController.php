<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cartItems = $request->session()->get('cart.items', []);
        $totalPrice = $this->calculateTotalPrice($cartItems);
        return view('cart.index', compact('cartItems', 'totalPrice'));
    }

    public function add(Request $request)
    {
        $isTicketmaster = $request->input('is_ticketmaster') === 'true' || str_starts_with($request->input('event_id', ''), 'tm_');

        $request->validate([
            'event_id' => 'required|string',
            'event_name' => $isTicketmaster ? 'required|string|max:255' : 'sometimes|string|max:255',
            'event_price' => $isTicketmaster ? 'required|numeric|min:0' : 'sometimes|numeric|min:0',
            'event_image' => 'nullable|string|url',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        if (!$isTicketmaster) {
            // Yerel etkinlikler için ek 'exists' validasyonu
            $request->validate([
                'event_id' => 'integer|exists:events,id',
            ]);
        }

        $eventId = $request->input('event_id');
        $quantity = (int)$request->input('quantity', 1);
        $cartItems = $request->session()->get('cart.items', []);

        // Kontenjan kontrolü
        if ($isTicketmaster) {
            $apiEventId = str_replace('tm_', '', $eventId);
            $apiEventState = \App\Models\ApiEventState::where('event_id', $apiEventId)->first();

            if (!$apiEventState) {
                return response()->json([
                    'success' => false,
                    'message' => 'Etkinlik durumu bulunamadı!'
                ], 422);
            }

            $customData = $apiEventState->custom_data ?? [];
            $availableCapacity = isset($customData['override_capacity']) ? (int)$customData['override_capacity'] : 0;

            // Sepetteki mevcut miktarı kontrol et
            $currentCartQuantity = isset($cartItems[$eventId]) ? $cartItems[$eventId]['quantity'] : 0;
            $totalRequestedQuantity = $currentCartQuantity + $quantity;

            if ($totalRequestedQuantity > $availableCapacity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Üzgünüz, bu etkinlik için yeterli kontenjan yok. Kalan kontenjan: ' . $availableCapacity
                ], 422);
            }
        } else {
            // Yerel etkinlikler için kontenjan kontrolü
            $event = Event::find((int)$eventId);
            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Etkinlik bulunamadı!'
                ], 422);
            }

            $availableCapacity = $event->capacity ?? $event->available_tickets ?? 0;
            $currentCartQuantity = isset($cartItems[$eventId]) ? $cartItems[$eventId]['quantity'] : 0;
            $totalRequestedQuantity = $currentCartQuantity + $quantity;

            if ($totalRequestedQuantity > $availableCapacity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Üzgünüz, bu etkinlik için yeterli kontenjan yok. Kalan kontenjan: ' . $availableCapacity
                ], 422);
            }
        }

        // Kontenjan kontrolü başarılı, sepete ekle
        if (isset($cartItems[$eventId])) {
            $cartItems[$eventId]['quantity'] += $quantity;
        } else {
            if ($isTicketmaster) {
                // Ticketmaster etkinliği: bilgileri request'ten al
                $eventPrice = (float)$request->input('event_price', 0);
                $adultPrice = isset($customData['adult_price']) ? (float)$customData['adult_price'] : $eventPrice;
                $childPrice = isset($customData['child_price']) ? (float)$customData['child_price'] : round($eventPrice * 0.5, 2);
                $cartItems[$eventId] = [
                    'id' => $eventId,
                    'name' => $request->input('event_name'),
                    'price' => $eventPrice,
                    'quantity' => $quantity,
                    'image_url' => $request->input('event_image', ''),
                    'is_ticketmaster' => true,
                    'child_price' => $childPrice,
                    'adult_price' => $adultPrice,
                    'ticket_types' => [
                        'adult' => 1, // Varsayılan olarak 1 adet tam bilet
                        'child' => 0  // Çocuk bileti başlangıçta 0
                    ]
                ];
            } else {
                // Yerel etkinlik: bilgileri DB'den al
                $event = Event::findOrFail((int)$eventId);
                $cartItems[$eventId] = [
                    'id' => $event->id,
                    'name' => $event->title,
                    'price' => (float)$request->input('event_price', $event->price ?? 0),
                    'quantity' => $quantity,
                    'image_url' => $event->image_url,
                    'is_ticketmaster' => false,
                    'child_price' => (float)($event->ticketTypes->where('name', 'Çocuk Bileti')->first()?->price ?? $event->price ?? 0),
                    'adult_price' => (float)($event->ticketTypes->where('name', 'Tam Bilet')->first()?->price ?? $event->price ?? 0),
                    'ticket_types' => [
                        'adult' => 1, // Varsayılan olarak 1 adet tam bilet
                        'child' => 0  // Çocuk bileti başlangıçta 0
                    ]
                ];
            }
        }

        $request->session()->put('cart.items', $cartItems);

        // Calculate current cart item count
        $cartItemCount = 0;
        foreach ($request->session()->get('cart.items', []) as $item) {
            $cartItemCount += $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'message' => 'Etkinlik sepete eklendi!',
            'cartItemCount' => $cartItemCount
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'event_id' => 'required|string' // event_id string olabilir (örn: tm_gfdgfd)
        ]);
        $eventId = $request->input('event_id');
        $cartItems = $request->session()->get('cart.items', []);

        if (isset($cartItems[$eventId])) {
            unset($cartItems[$eventId]);
            $request->session()->put('cart.items', $cartItems);
            return redirect()->route('cart.index')->with('success', 'Etkinlik sepetten çıkarıldı.');
        }
        return redirect()->route('cart.index')->with('error', 'Etkinlik sepette bulunamadı.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'event_id' => 'required|string', // event_id string olabilir
            'ticket_types' => 'required|array',
            'ticket_types.adult' => 'required|integer|min:0',
            'ticket_types.child' => 'required|integer|min:0'
        ]);

        $eventId = $request->input('event_id');
        $ticketTypes = $request->input('ticket_types');
        $cartItems = $request->session()->get('cart.items', []);

        if (isset($cartItems[$eventId])) {
            // Toplam bilet sayısını kontrol et
            $totalTickets = $ticketTypes['adult'] + $ticketTypes['child'];
            if ($totalTickets < 1) {
                return redirect()->route('cart.index')->with('error', 'En az 1 bilet seçmelisiniz.');
            }

            // Kontenjan kontrolü
            if ($cartItems[$eventId]['is_ticketmaster']) {
                $apiEventId = str_replace('tm_', '', $eventId);
                $apiEventState = \App\Models\ApiEventState::where('event_id', $apiEventId)->first();
                if ($apiEventState) {
                    $customData = $apiEventState->custom_data ?? [];
                    $availableCapacity = isset($customData['override_capacity']) ? (int)$customData['override_capacity'] : 0;
                    if ($totalTickets > $availableCapacity) {
                        return redirect()->route('cart.index')->with('error', 'Üzgünüz, bu etkinlik için yeterli kontenjan yok. Kalan kontenjan: ' . $availableCapacity);
                    }
                }
            } else {
                $event = Event::find((int)$eventId);
                if ($event) {
                    $availableCapacity = $event->capacity ?? $event->available_tickets ?? 0;
                    if ($totalTickets > $availableCapacity) {
                        return redirect()->route('cart.index')->with('error', 'Üzgünüz, bu etkinlik için yeterli kontenjan yok. Kalan kontenjan: ' . $availableCapacity);
                    }
                }
            }

            // Bilet türlerini güncelle
            $cartItems[$eventId]['ticket_types'] = [
                'adult' => (int)$ticketTypes['adult'],
                'child' => (int)$ticketTypes['child']
            ];
            $request->session()->put('cart.items', $cartItems);
            return redirect()->route('cart.index')->with('success', 'Sepet güncellendi.');
        }
        return redirect()->route('cart.index')->with('error', 'Etkinlik sepette bulunamadı.');
    }

    private function calculateTotalPrice(array $cartItems)
    {
        $total = 0;
        foreach ($cartItems as $item) {
            $adultTotal = ($item['adult_price'] ?? 0) * ($item['ticket_types']['adult'] ?? 0);
            $childTotal = ($item['child_price'] ?? 0) * ($item['ticket_types']['child'] ?? 0);
            $total += $adultTotal + $childTotal;
        }
        return $total;
    }

    /**
     * Sepet için ödeme işlemi ve kontenjan azaltma
     */
    public function checkout(Request $request)
    {
        $cartItems = $request->session()->get('cart.items', []);
        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Sepetiniz boş!');
        }

        $insufficientEvents = [];
        $localEventsToUpdate = [];
        $apiEventsToUpdate = [];

        foreach ($cartItems as $item) {
            $totalTickets = ($item['ticket_types']['adult'] ?? 0) + ($item['ticket_types']['child'] ?? 0);
            if ($item['is_ticketmaster'] ?? false) {
                // API etkinliği için kontenjan kontrolü
                $eventId = str_replace('tm_', '', $item['id']); // tm_ önekini kaldır
                \Log::info('Cart Checkout - API Event ID dönüşümü:', [
                    'orijinal_id' => $item['id'],
                    'dönüştürülmüş_id' => $eventId
                ]);
                $apiEventState = \App\Models\ApiEventState::where('event_id', $eventId)->first();

                if (!$apiEventState) {
                    \Log::warning('Cart Checkout - ApiEventState bulunamadı:', [
                        'event_id' => $item['id'],
                        'arama_yapılan_id' => $eventId,
                        'item_name' => $item['name']
                    ]);
                    $insufficientEvents[] = $item['name'] . ' (Etkinlik durumu bulunamadı)';
                    continue;
                }

                \Log::info('Cart Checkout - ApiEventState custom_data (veritabanından gelen):', [
                    'event_id' => $item['id'],
                    'custom_data' => $apiEventState->custom_data
                ]);

                $customData = $apiEventState->custom_data ?? [];
                $overrideCapacity = isset($customData['override_capacity']) ? (int)$customData['override_capacity'] : 0;

                if ($overrideCapacity < $totalTickets) {
                    $insufficientEvents[] = $item['name'] . ' (Kalan: ' . $overrideCapacity . ')';
                } else {
                    $apiEventsToUpdate[] = [
                        'apiEventState' => $apiEventState,
                        'quantity' => $totalTickets
                    ];
                }
            } else {
                // Yerel etkinlik için kontenjan kontrolü
                $event = Event::find($item['id']);
                if (!$event) {
                    $insufficientEvents[] = $item['name'] . ' (Etkinlik bulunamadı)';
                    continue;
                }
                $kontenjan = $event->capacity ?? $event->available_tickets ?? 0;
                if ($kontenjan < $totalTickets) {
                    $insufficientEvents[] = $item['name'] . ' (Kalan: ' . $kontenjan . ')';
                } else {
                    $localEventsToUpdate[] = [
                        'event' => $event,
                        'quantity' => $totalTickets
                    ];
                }
            }
        }

        if (!empty($insufficientEvents)) {
            return redirect()->route('cart.index')->with('error', 'Bazı etkinliklerde yeterli kontenjan yok: ' . implode(', ', $insufficientEvents));
        }

        // Yerel etkinliklerin kontenjanını düşür
        foreach ($localEventsToUpdate as $data) {
            $event = $data['event'];
            $event->capacity = max(0, ($event->capacity ?? $event->available_tickets ?? 0) - $data['quantity']);
            $event->save();
        }

        // API etkinliklerinin kontenjanını düşür
        foreach ($apiEventsToUpdate as $data) {
            $apiEventState = $data['apiEventState'];
            $customData = $apiEventState->custom_data ?? [];
            \Log::info('Cart Checkout - API Etkinlik Kontenjan Güncelleme (Önce):', [
                'event_id' => $apiEventState->event_id,
                'mevcut_kontenjan' => $customData['override_capacity'] ?? 0,
                'düşülecek_miktar' => $data['quantity'],
                'custom_data' => $customData
            ]);
            $customData['override_capacity'] = max(0, ($customData['override_capacity'] ?? 0) - $data['quantity']);
            \Log::info('Cart Checkout - API Etkinlik Kontenjan Güncelleme (Sonra):', [
                'event_id' => $apiEventState->event_id,
                'yeni_kontenjan' => $customData['override_capacity'],
                'custom_data' => $customData
            ]);
            $apiEventState->custom_data = $customData;
            $apiEventState->save();
            \Log::info('Cart Checkout - API Etkinlik Kontenjan Güncelleme (Kayıt Sonrası):', [
                'event_id' => $apiEventState->event_id,
                'kaydedilen_custom_data' => $apiEventState->fresh()->custom_data
            ]);
        }

        $request->session()->forget('cart.items');

        return redirect()->route('cart.index')->with('success', 'Ödeme başarılı! Biletleriniz ayrıldı.');
    }

    // Gelecekte eklenebilecek diğer sepet metodları (add, update, remove vb.)
}
