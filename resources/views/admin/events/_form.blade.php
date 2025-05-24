@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Etkinlik Başlığı</label>
        <input type="text" name="title" id="title" value="{{ old('title', $event->title ?? '') }}" required 
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('title') border-red-500 @enderror">
        @error('title')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="type" class="block text-sm font-medium text-gray-700">Etkinlik Türü</label>
        <input type="text" name="type" id="type" value="{{ old('type', $event->type ?? '') }}" required
               placeholder="Örn: Konser, Tiyatro, Spor" 
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('type') border-red-500 @enderror">
        @error('type')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-6">
    <label for="description" class="block text-sm font-medium text-gray-700">Açıklama</label>
    <textarea name="description" id="description" rows="4" required 
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-500 @enderror">{{ old('description', $event->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <div>
        <label for="event_date" class="block text-sm font-medium text-gray-700">Etkinlik Tarihi ve Saati</label>
        <input type="datetime-local" name="event_date" id="event_date" 
               value="{{ old('event_date', isset($event) && $event->event_date ? $event->event_date->format('Y-m-d\\TH:i') : '') }}" required
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('event_date') border-red-500 @enderror">
        @error('event_date')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="location" class="block text-sm font-medium text-gray-700">Konum</label>
        <input type="text" name="location" id="location" value="{{ old('location', $event->location ?? '') }}" required
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('location') border-red-500 @enderror">
        @error('location')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <div>
        <label for="capacity" class="block text-sm font-medium text-gray-700">Genel Kontenjan</label>
        <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $event->capacity ?? '0') }}" min="0"
               placeholder="Toplam bilet sayısı (isteğe bağlı)"
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('capacity') border-red-500 @enderror">
        @error('capacity')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-6">
    <h3 class="text-lg font-medium text-gray-900 mb-3">Bilet Türleri</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="ticket_types_child_price" class="block text-sm font-medium text-gray-700">Çocuk Bileti Fiyatı (₺)</label>
            <input type="number" name="ticket_types[child][price]" id="ticket_types_child_price" value="{{ old('ticket_types.child.price', isset($event) ? optional($event->ticketTypes->where('name', 'Çocuk Bileti')->first())->price : '') }}" min="0" step="0.01"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div>
            <label for="ticket_types_adult_price" class="block text-sm font-medium text-gray-700">Tam Bilet Fiyatı (₺)</label>
            <input type="number" name="ticket_types[adult][price]" id="ticket_types_adult_price" value="{{ old('ticket_types.adult.price', isset($event) ? optional($event->ticketTypes->where('name', 'Tam Bilet')->first())->price : '') }}" min="0" step="0.01"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
    </div>
    <small class="text-gray-500">Sadece iki bilet türü vardır: Çocuk Bileti ve Tam Bilet. Fiyatları buradan belirleyebilirsiniz.</small>
</div>

<div class="mt-6">
    <label for="categories" class="block text-sm font-medium text-gray-700">Kategoriler (Virgülle ayırın)</label>
    <input type="text" name="categories_input" id="categories_input" 
           value="{{ old('categories_input', isset($event) && is_array($event->categories) ? implode(', ', $event->categories) : (isset($event) && is_string($event->categories) ? $event->categories : '')) }}" 
           placeholder="Örn: Müzik, Festival, Gençlik"
           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('categories') border-red-500 @enderror">
    <small class="text-gray-500">Controller tarafında bu 'categories_input' alanı işlenip 'categories' dizisine dönüştürülmelidir.</small>
    @error('categories')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
    @error('categories.*')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
    {{-- Controller'da categories normalde bir array bekliyor, bu yüzden input'tan gelen string'i parse etmemiz gerekecek. --}}
    {{-- Alternatif olarak, bu input'un adını categories[] yapıp, controller'da array olarak alabiliriz. --}}
    {{-- Veya JS ile yönetilen bir tag input kullanılabilir. Şimdilik basit bir text input bırakıyorum. --}}
    {{-- Controller'daki 'categories' => 'nullable|array' validasyonu için bu alanı store/update metodunda işleyeceğiz --}}
</div>

<div class="mt-6">
    <label for="image" class="block text-sm font-medium text-gray-700">Etkinlik Görseli</label>
    <input type="file" name="image" id="image" accept="image/*"
           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 @error('image') border-red-500 @enderror">
    @if(isset($event) && $event->image_url)
        <div class="mt-2">
            <p class="text-xs text-gray-500">Mevcut Görsel:</p>
            <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="h-20 w-auto rounded mt-1">
        </div>
    @endif
    @error('image')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input id="weather_dependent" name="weather_dependent" type="checkbox" value="1" 
                   {{ old('weather_dependent', isset($event) && $event->weather_dependent ? 'checked' : '') }} 
                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
        </div>
        <div class="ml-3 text-sm">
            <label for="weather_dependent" class="font-medium text-gray-700">Hava Koşullarına Bağlı mı?</label>
        </div>
    </div>

    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input id="is_published" name="is_published" type="checkbox" value="1" 
                   {{ old('is_published', isset($event) && $event->is_published ? 'checked' : '') }} 
                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
        </div>
        <div class="ml-3 text-sm">
            <label for="is_published" class="font-medium text-gray-700">Yayınlansın mı?</label>
        </div>
    </div>
</div>

<div class="mt-8 pt-5">
    <div class="flex justify-end">
        <a href="{{ route('admin.events.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
            İptal
        </a>
        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
            {{ isset($event) ? 'Güncelle' : 'Kaydet' }}
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('ticket-types-container');
    const addButton = document.getElementById('add-ticket-type');
    let ticketTypeIndex = {{ (isset($event) && $event->ticketTypes->count() > 0) ? $event->ticketTypes->count() : 1 }}; // Başlangıç index'i

    if (addButton && container) {
        addButton.addEventListener('click', function () {
            const newItem = document.createElement('div');
            newItem.classList.add('ticket-type-item', 'p-4', 'border', 'border-gray-300', 'rounded-md', 'mt-4');
            newItem.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="ticket_types_${ticketTypeIndex}_name" class="block text-sm font-medium text-gray-700">Tür Adı</label>
                        <input type="text" name="ticket_types[${ticketTypeIndex}][name]" id="ticket_types_${ticketTypeIndex}_name" placeholder="VIP, Standart vb."
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="ticket_types_${ticketTypeIndex}_price" class="block text-sm font-medium text-gray-700">Fiyat (₺)</label>
                        <input type="number" name="ticket_types[${ticketTypeIndex}][price]" id="ticket_types_${ticketTypeIndex}_price" min="0" step="0.01"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="ticket_types_${ticketTypeIndex}_quantity" class="block text-sm font-medium text-gray-700">Miktar</label>
                        <input type="number" name="ticket_types[${ticketTypeIndex}][quantity]" id="ticket_types_${ticketTypeIndex}_quantity" min="0"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
                <div class="mt-2 text-right">
                    <button type="button" class="remove-ticket-type text-red-600 hover:text-red-800 text-sm">Bu Türü Kaldır</button>
                </div>
            `;
            container.appendChild(newItem);
            ticketTypeIndex++;
        });

        container.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-ticket-type')) {
                // Konteynerde en az bir ticket-type-item kalmasını sağla (opsiyonel)
                // if (container.querySelectorAll('.ticket-type-item').length > 1) {
                //     e.target.closest('.ticket-type-item').remove();
                // }
                e.target.closest('.ticket-type-item').remove();
            }
        });
    }
});
</script> 