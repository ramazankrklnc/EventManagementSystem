@extends('layouts.app')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <!-- Geri Dön Butonu -->
        <div class="mb-6">
            <a href="{{ route('events.index') }}" class="text-blue-500 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-1"></i> Etkinliklere Dön
            </a>
        </div>

        <!-- Etkinlik Detayları -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Sol Taraf: Görsel -->
            <div>
                @if($event->image_path)
                    <img src="{{ Storage::url($event->image_path) }}" alt="{{ $event->title }}" class="w-full h-96 object-cover rounded-lg shadow-md">
                @else
                    <div class="w-full h-96 bg-gray-200 rounded-lg shadow-md flex items-center justify-center">
                        <span class="text-gray-400 text-lg">Görsel Yok</span>
                    </div>
                @endif
            </div>

            <!-- Sağ Taraf: Bilgiler -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $event->title }}</h1>

                <!-- Etkinlik Durumu -->
                <div class="mb-4">
                    @if($event->isActive())
                        <span class="px-3 py-1 text-sm font-semibold text-green-800 bg-green-100 rounded-full">
                            Aktif
                        </span>
                    @elseif($event->isCancelled())
                        <span class="px-3 py-1 text-sm font-semibold text-red-800 bg-red-100 rounded-full">
                            İptal Edildi
                        </span>
                    @else
                        <span class="px-3 py-1 text-sm font-semibold text-gray-800 bg-gray-100 rounded-full">
                            Tamamlandı
                        </span>
                    @endif
                </div>

                <!-- Hava Durumu Bilgisi -->
                @if($event->weather_dependent && isset($event->weather_status))
                    <div class="mb-6">
                        @if($event->weather_status['status'] === 'good')
                            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg">
                                <div class="font-semibold">Hava Durumu: Uygun</div>
                                <div class="text-sm">{{ $event->weather_status['temperature'] }}°C - {{ $event->weather_status['condition'] }}</div>
                            </div>
                        @elseif($event->weather_status['status'] === 'risky')
                            <div class="bg-yellow-100 text-yellow-800 px-4 py-3 rounded-lg">
                                <div class="font-semibold">Dikkat!</div>
                                <div class="text-sm">{{ $event->weather_status['message'] }}</div>
                                <div class="text-sm">{{ $event->weather_status['temperature'] }}°C - {{ $event->weather_status['condition'] }}</div>
                            </div>
                        @else
                            <div class="bg-gray-100 text-gray-800 px-4 py-3 rounded-lg">
                                <div class="font-semibold">Hava Durumu Bilgisi Yok</div>
                                <div class="text-sm">{{ $event->weather_status['message'] }}</div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Etkinlik Bilgileri -->
                <div class="space-y-4 mb-6">
                    <div class="flex items-start">
                        <i class="far fa-calendar-alt text-gray-500 mt-1 mr-3"></i>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Tarih ve Saat</h3>
                            <p class="text-gray-900">{{ $event->event_date->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt text-gray-500 mt-1 mr-3"></i>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Konum</h3>
                            <p class="text-gray-900">{{ $event->location }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <i class="fas fa-ticket-alt text-gray-500 mt-1 mr-3"></i>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Bilet Fiyatı</h3>
                            <p class="text-gray-900">{{ number_format($event->ticket_price, 2) }} TL</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <i class="fas fa-ticket-alt text-gray-500 mt-1 mr-3"></i>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Kalan Bilet</h3>
                            <p class="text-gray-900">{{ $event->available_tickets }} / {{ $event->total_tickets }}</p>
                        </div>
                    </div>
                </div>

                <!-- Açıklama -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Açıklama</h3>
                    <p class="text-gray-900 whitespace-pre-line">{{ $event->description }}</p>
                </div>

                <!-- Etkinlik Sahibi -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Etkinlik Sahibi</h3>
                    <p class="text-gray-900">{{ $event->user->name }}</p>
                </div>

                <!-- Aksiyon Butonları -->
                <div class="flex space-x-4">
                    @if($event->canPurchaseTicket())
                        <form action="{{ route('cart.add', $event) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-lg shadow-lg">
                                Bilet Al
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 