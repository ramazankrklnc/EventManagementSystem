@extends('layouts.admin')

@section('content')
<div class="py-12 bg-gray-900">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
                <svg class="h-8 w-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <div class="ml-4">
                    <h2 class="text-2xl font-semibold text-white">API Etkinliğini Düzenle</h2>
                    {{-- eventDetails Ticketmaster API'sinden gelen ham veriyi içerir --}}
                    <p class="text-gray-400 mt-1">{{ $eventDetails['name'] ?? 'Başlık Yok' }} (ID: {{ $apiEventState->event_id }})</p>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-900 border-l-4 border-red-500 text-red-100 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-gray-800 shadow-xl rounded-lg overflow-hidden">
            <div class="p-6">
                <form action="{{ route('admin.events.updateApiEvent', $apiEventState->event_id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Etkinlik Detayları (API'dan gelen) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-400">Etkinlik Adı (API)</label>
                            <div class="mt-1 p-3 bg-gray-700 rounded-md">
                                <p class="text-white">{{ $eventDetails['name'] ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400">Etkinlik Tarihi (API)</label>
                            <div class="mt-1 p-3 bg-gray-700 rounded-md">
                                <p class="text-white">
                                    {{ isset($eventDetails['dates']['start']['dateTime']) ? \Carbon\Carbon::parse($eventDetails['dates']['start']['dateTime'])->format('d.m.Y H:i') : 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400">Konum (API)</label>
                            <div class="mt-1 p-3 bg-gray-700 rounded-md">
                                <p class="text-white">{{ $eventDetails['_embedded']['venues'][0]['name'] ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400">Tür (API)</label>
                            <div class="mt-1 p-3 bg-gray-700 rounded-md">
                                <p class="text-white">{{ $eventDetails['classifications'][0]['segment']['name'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-400">Fiyat Aralığı (API)</label>
                            <div class="mt-1 p-3 bg-gray-700 rounded-md">
                                <p class="text-white">{{ $priceInfoApi ?? 'Belirlenmedi' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Yayın Durumu -->
                    <div class="mt-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_published" id="is_published" value="1"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                   {{ old('is_published', $apiEventState->is_published) ? 'checked' : '' }}>
                            <label for="is_published" class="ml-2 block text-sm text-gray-400">
                                Etkinlik yayında
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Bu seçenek etkinliğin ana sayfada görünüp görünmeyeceğini belirler.
                        </p>
                    </div>

                    <!-- Özel Fiyat ve Kontenjan -->
                    <div class="mt-6 pt-6 border-t border-gray-700">
                        <h4 class="text-md font-semibold text-gray-300 mb-3">Özel Fiyat ve Kontenjan Ayarları</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="custom_data_child_price" class="block text-sm font-medium text-gray-400">Çocuk Bileti Fiyatı (₺)</label>
                                @php
                                    $mainPrice = $apiEventState->custom_data['override_price'] ?? $apiEventState->price ?? 0;
                                    $childPrice = $apiEventState->custom_data['child_price'] ?? round($mainPrice * 0.5, 2);
                                    $adultPrice = $apiEventState->custom_data['adult_price'] ?? $mainPrice;
                                @endphp
                                <input type="number" name="custom_data[child_price]" id="custom_data_child_price"
                                       value="{{ old('custom_data.child_price', $childPrice) }}" min="0" step="0.01"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md shadow-sm bg-gray-800 text-gray-100 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('custom_data.child_price')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="custom_data_adult_price" class="block text-sm font-medium text-gray-400">Tam Bilet Fiyatı (₺)</label>
                                <input type="number" name="custom_data[adult_price]" id="custom_data_adult_price"
                                       value="{{ old('custom_data.adult_price', $adultPrice) }}" min="0" step="0.01"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md shadow-sm bg-gray-800 text-gray-100 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('custom_data.adult_price')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="override_capacity" class="block text-sm font-medium text-gray-400">Özel Kontenjan</label>
                                <input type="number" name="custom_data[override_capacity]" id="override_capacity" 
                                       value="{{ old('custom_data.override_capacity', $apiEventState->custom_data['override_capacity'] ?? '') }}" 
                                       placeholder="Boş bırakırsanız API kontenjanı kullanılır (varsa)"
                                       min="0"
                                       class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-white">
                                @error('custom_data.override_capacity')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            Bu alanları boş bırakırsanız veya 0 girerseniz, etkinlik listesinde ve detaylarında API'den gelen orijinal veriler (eğer varsa) veya "Belirlenmedi" gösterilir.
                        </p>
                    </div>

                    <!-- Butonlar -->
                    <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-700">
                        <a href="{{ route('admin.events.index') }}" 
                           class="px-4 py-2 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                            İptal
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Değişiklikleri Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 