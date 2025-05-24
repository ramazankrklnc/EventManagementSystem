@extends('layouts.admin')

@section('content')
<div class="py-12 bg-gray-900">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
                <svg class="h-8 w-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <div class="ml-4">
                    <h2 class="text-2xl font-semibold text-white">Etkinlik Yönetimi</h2>
                    <p class="text-gray-400 mt-1">Son güncelleme: {{ now()->format('d.m.Y H:i') }}</p>
                </div>
            </div>
            <a href="{{ route('admin.events.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Yeni Etkinlik Ekle
            </a>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-green-900 border-l-4 border-green-500 text-green-100 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="grid gap-6 mb-8">
            <!-- Etkinlik Listesi -->
            <div class="bg-gray-800 shadow-xl rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead>
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                        Etkinlik Bilgileri
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                        Tarih
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                        Fiyat / Kontenjan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">
                                        Durum
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">
                                        İşlemler
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @forelse($events as $event)
                                <tr class="hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-white">
                                                    {{ $event->title }}
                                                </div>
                                                <div class="text-xs text-gray-400">
                                                    ID: {{ $event->id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-300">
                                            @if($event->event_date)
                                                {{ $event->event_date->format('d.m.Y') }}
                                                <div class="text-xs text-gray-400">
                                                    {{ $event->event_date->format('H:i') }}
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-500">Tarih belirtilmemiş</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($event->source === 'local')
                                            @if($event->ticketTypes && $event->ticketTypes->count() > 0)
                                                @php
                                                    $minPrice = $event->ticketTypes->min('price');
                                                    $maxPrice = $event->ticketTypes->max('price');
                                                    $totalCapacity = $event->capacity ?? 'N/A';
                                                    $soldTickets = 0; // Bu bilgi siparişlerden çekilmeli, şimdilik 0
                                                    $remainingCapacity = is_numeric($totalCapacity) ? $totalCapacity - $soldTickets : 'N/A';
                                                @endphp
                                                <div class="text-sm text-gray-300">
                                                    @if($minPrice == $maxPrice)
                                                        {{ number_format($minPrice, 2) }} TL
                                                    @else
                                                        {{ number_format($minPrice, 2) }} - {{ number_format($maxPrice, 2) }} TL
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-400">
                                                    Kontenjan: {{ $remainingCapacity }} / {{ $totalCapacity }}
                                                </div>
                                            @else
                                                <span class="text-xs text-yellow-400">Bilet türü tanımlanmamış</span>
                                            @endif
                                            @if($event->capacity === null && (!$event->ticketTypes || $event->ticketTypes->count() == 0))
                                                <div class="text-xs text-gray-500">Genel kontenjan belirtilmemiş</div>
                                            @endif
                                        @elseif($event->source === 'ticketmaster')
                                            @php
                                                $customData = is_array($event->custom_data) ? $event->custom_data : [];
                                                $mainPrice = $customData['override_price'] ?? $event->price ?? 0;
                                                $childPrice = $customData['child_price'] ?? round($mainPrice * 0.5, 2);
                                                $adultPrice = $customData['adult_price'] ?? $mainPrice;
                                                $overrideCapacity = $customData['override_capacity'] ?? null;
                                            @endphp
                                            <div class="text-sm text-gray-300">
                                                @if($childPrice > 0 || $adultPrice > 0)
                                                    Çocuk: {{ number_format($childPrice, 2) }} TL<br>
                                                    Tam: {{ number_format($adultPrice, 2) }} TL
                                                @else
                                                    <div class="text-sm text-gray-400">Belirlenmedi</div>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                @if($overrideCapacity !== null)
                                                    Kontenjan: {{ $overrideCapacity }} (Özel)
                                                @else
                                                    Kontenjan: API
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full {{ $event->is_published ? 'bg-green-900 text-green-200' : 'bg-yellow-900 text-yellow-200' }}">
                                            {{ $event->is_published ? 'Yayında' : 'Taslak' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end space-x-3">
                                            @if(!isset($event->is_api_event))
                                                <form action="{{ route('admin.events.togglePublish', $event->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                        class="px-3 py-1 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                                        {{ $event->is_published ? 'Yayından Kaldır' : 'Yayınla' }}
                                                    </button>
                                                </form>
                                                <a href="{{ route('admin.events.edit', $event->id) }}" 
                                                   class="px-3 py-1 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                                    Düzenle
                                                </a>
                                                <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                        onclick="return confirm('Bu etkinliği silmek istediğinize emin misiniz?')"
                                                        class="px-3 py-1 bg-red-600 text-white text-xs rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                                        Sil
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.events.toggleApiEventPublish', $event->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                        class="px-3 py-1 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                                        {{ $event->is_published ? 'Yayından Kaldır' : 'Yayınla' }}
                                                    </button>
                                                </form>
                                                <a href="{{ route('admin.events.editApiEvent', $event->id) }}" 
                                                   class="px-3 py-1 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                                    Düzenle
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="h-12 w-12 text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L3 10.25V5.75C3 4.64543 3.89543 3.75 5 3.75H9.5M9.75 17L11.25 14.25M9.75 17H14.25M15 12.75L14.25 10.5M15 12.75L12.75 15M15 12.75V9M15 12.75L18.75 9M15 12.75H18.75M3 10.25L9.75 3.5M3 10.25H6M18.75 9L15 5.25M18.75 9H15" />
                                            </svg>
                                            <p class="text-gray-300 font-medium mb-2">Henüz hiç etkinlik eklenmemiş.</p>
                                            <p class="text-gray-400 text-xs mb-4">Başlamak için yeni bir etkinlik oluşturun.</p>
                                            <a href="{{ route('admin.events.create') }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                                                İlk Etkinliği Ekle
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if(isset($hasPages) && $hasPages && $events->hasPages())
                <div class="px-6 py-3 bg-gray-700 border-t border-gray-600">
                    {{ $events->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 