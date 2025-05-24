@extends('layouts.app')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <!-- Başlık -->
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Etkinliği Düzenle</h1>
        </div>

        <!-- Form -->
        <form action="{{ route('events.update', $event) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Etkinlik Adı -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Etkinlik Adı</label>
                <input type="text" name="title" id="title" value="{{ old('title', $event->title) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Açıklama -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Açıklama</label>
                <textarea name="description" id="description" rows="4" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $event->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tarih ve Saat -->
            <div>
                <label for="event_date" class="block text-sm font-medium text-gray-700">Tarih ve Saat</label>
                <input type="datetime-local" name="event_date" id="event_date" 
                    value="{{ old('event_date', $event->event_date->format('Y-m-d\TH:i')) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('event_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Konum -->
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700">Konum</label>
                <input type="text" name="location" id="location" value="{{ old('location', $event->location) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('location')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bilet Fiyatı -->
            <div>
                <label for="ticket_price" class="block text-sm font-medium text-gray-700">Bilet Fiyatı (TL)</label>
                <input type="number" name="ticket_price" id="ticket_price" 
                    value="{{ old('ticket_price', $event->ticket_price) }}" step="0.01" min="0" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('ticket_price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Toplam Bilet Sayısı -->
            <div>
                <label for="total_tickets" class="block text-sm font-medium text-gray-700">Toplam Bilet Sayısı</label>
                <input type="number" name="total_tickets" id="total_tickets" 
                    value="{{ old('total_tickets', $event->total_tickets) }}" min="1" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('total_tickets')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Etkinlik Durumu -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Etkinlik Durumu</label>
                <select name="status" id="status" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="active" {{ old('status', $event->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="cancelled" {{ old('status', $event->status) === 'cancelled' ? 'selected' : '' }}>İptal Edildi</option>
                    <option value="completed" {{ old('status', $event->status) === 'completed' ? 'selected' : '' }}>Tamamlandı</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mevcut Görsel -->
            @if($event->image_path)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mevcut Görsel</label>
                    <img src="{{ Storage::url($event->image_path) }}" alt="{{ $event->title }}" 
                        class="mt-2 h-32 w-32 object-cover rounded-lg">
                </div>
            @endif

            <!-- Yeni Görsel -->
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700">Yeni Görsel (Opsiyonel)</label>
                <input type="file" name="image" id="image" accept="image/*"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Butonlar -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('events.show', $event) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    İptal
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Değişiklikleri Kaydet
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 