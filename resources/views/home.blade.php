@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-slate-900 via-purple-950 to-black text-gray-200 selection:bg-purple-600 selection:text-white">
    <div class="container mx-auto px-4 py-12 md:py-16 lg:py-20">
        
        <!-- Üst Bölüm: Hero ve Hava Durumu -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-8 md:gap-12 lg:gap-16 mb-16 md:mb-24 min-h-[auto] md:min-h-[calc(70vh)]">

            <!-- Hero Section (Sol Taraf) -->
            <div class="md:w-1/2 lg:w-3/5 text-center md:text-left animate-fade-in">
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-pink-500 to-purple-600 mb-6 md:mb-8 leading-tight">
                    Hoş Geldiniz
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl text-gray-300 mb-10 md:mb-12 animate-fade-in-delay max-w-xl mx-auto md:mx-0" style="animation-delay: 0.2s;">
                    Şehirdeki en son etkinlikleri ve önemli duyuruları buradan takip et. Heyecan verici anları kaçırma!
                </p>
                <a href="#events-section-anchor" 
                   class="inline-block bg-gradient-to-r from-pink-500 via-purple-600 to-indigo-700 hover:from-pink-600 hover:via-purple-700 hover:to-indigo-800 text-white font-semibold py-3 sm:py-4 px-8 sm:px-10 rounded-lg shadow-xl text-md sm:text-lg transform hover:scale-105 hover:shadow-2xl transition-all duration-300 animate-fade-in-delay focus:outline-none focus:ring-4 focus:ring-purple-400 focus:ring-opacity-50" 
                   style="animation-delay: 0.4s;">
                    <span class="mr-2">✨</span> Etkinlikleri Keşfet!
                </a>
            </div>

            <!-- Weather Section (Sağ Taraf) -->
            <div class="md:w-1/2 lg:w-2/5 w-full max-w-md animate-fade-in-delay" style="animation-delay: 0.6s;">
                <div id="weather-widget" class="bg-slate-800/70 dark:bg-slate-800/70 backdrop-blur-md rounded-2xl shadow-2xl p-6 text-gray-100 transition-transform duration-300 hover:scale-[1.02]">
                    <div id="weather-loading" class="flex justify-center items-center py-8">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-400"></div>
                    </div>
                    <div id="weather-content" class="hidden">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-2xl font-semibold text-gray-50">Hava Durumu</h3>
                            <span id="weather-location" class="text-blue-300 font-medium text-lg"></span>
                        </div>
                        <div class="flex flex-col sm:flex-row items-center justify-around sm:space-x-4 my-6 text-center sm:text-left">
                            <img id="weather-icon" src="" alt="Hava Durumu İkonu" class="w-24 h-24 mb-3 sm:mb-0">
                            <div class="sm:ml-4">
                                <div id="weather-temp" class="text-6xl font-bold text-gray-50"></div>
                                <div id="weather-desc" class="text-gray-300 capitalize text-lg mt-1"></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t border-slate-700/80">
                            <div class="text-center">
                                <div class="text-gray-300 text-sm uppercase tracking-wider">Nem</div>
                                <div id="weather-humidity" class="font-semibold text-gray-50 text-2xl mt-1"></div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-300 text-sm uppercase tracking-wider">Rüzgar</div>
                                <div id="weather-wind" class="font-semibold text-gray-50 text-2xl mt-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- Üst Bölüm Sonu -->

        <!-- Events Section -->
        <section id="events-section-anchor" class="mb-16 md:mb-24 scroll-mt-20">
            <div class="bg-black/20 backdrop-blur-md p-6 md:p-8 rounded-2xl shadow-2xl">
                <h2 class="text-4xl font-bold mb-10 text-center text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-500 flex items-center justify-center">
                    <span class="mr-3 text-4xl">🎉</span>
                    Etkinlikler
                </h2>

                <div id="event-type-filters" class="mb-8 text-center space-x-2 space-y-2 md:space-y-0">
                    <button data-type="all" class="event-filter-btn bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition-all duration-150 ease-in-out transform hover:scale-105"> 
Tümü
                    </button>
                    <!-- Diğer filtre butonları JavaScript ile buraya eklenecek -->
                </div>

                <div id="events-loading" class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-purple-400"></div>
                </div>
                <div id="events-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 opacity-0 transition-opacity duration-500"></div>
                <p id="no-events" class="text-center text-gray-400 py-8 text-xl" style="display: none;">
                    Filtreyle eşleşen etkinlik bulunamadı.
                </p>
            </div>
        </section>

        <!-- Announcements Section -->
        <section class="mb-16 md:mb-24">
            <div class="bg-black/20 backdrop-blur-md p-6 md:p-8 rounded-2xl shadow-2xl">
                <h2 class="text-4xl font-bold mb-10 text-center text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-indigo-500 flex items-center justify-center">
                    <span class="mr-3 text-4xl">📢</span>
                    Duyurular
                </h2>
                <div id="announcements-loading" class="flex justify-center items-center py-12" style="display: none;">
                    <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-sky-400"></div>
                </div>
                
                @if(isset($announcements) && $announcements->count() > 0)
                    <div id="announcements-list" class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 max-w-4xl mx-auto">
                        @foreach($announcements as $announcement)
                            <div class="bg-slate-800 rounded-xl shadow-xl overflow-hidden transform transition-all duration-300 hover:scale-[1.03] hover:shadow-purple-500/30">
                                <div class="p-6">
                                    <h4 class="text-xl font-semibold text-gray-100 mb-2 hover:text-sky-300 transition-colors duration-300">
                                        {{ $announcement->title }}
                                    </h4>
                                    <p class="text-gray-400 text-sm mb-4 leading-relaxed line-clamp-3">
                                        {{ $announcement->content }}
                                    </p>
                                    <div class="flex items-center text-xs text-sky-400/80 mt-auto pt-3 border-t border-slate-700/50">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span>{{ $announcement->created_at->translatedFormat('d F Y, H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div id="no-announcements" class="text-center text-gray-500 py-8">
                        <i class="fas fa-info-circle fa-3x mb-4 opacity-70"></i>
                        <p class="text-xl">Şu anda gösterilecek duyuru bulunmamaktadır.</p>
                    </div>
                @endif
            </div>
        </section>
        
    </div> <!-- container mx-auto sonu -->
</div> <!-- min-h-screen sonu -->

<style>
    html {
        scroll-behavior: smooth;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fade-in {
        animation: fadeIn 0.7s ease-out forwards;
    }

    .animate-fade-in-delay {
        animation: fadeIn 0.7s ease-out forwards; 
        opacity: 0; 
    }

    /* Event card hover efekti için Tailwind kullanılacak, float animasyonu kaldırıldı */
</style>

<script>
    // CSRF token ve rota URL'sini Blade'den JavaScript'e aktarma
    const csrfToken = '{{ csrf_token() }}';
    const cartAddUrl = '{{ route("cart.add") }}';
    // Controller'dan gelen 5 günlük hava durumu tahmin listesini alıyoruz.
    const weatherForecastList = @json($weather['list'] ?? []);

    /**
     * Belirli bir etkinlik tarihi için hava durumu açıklamasını döndürür.
     * @param {string} eventDateStr - Etkinlik tarihi string'i (new Date() ile parse edilebilir formatta).
     * @param {Array} forecastList - OpenWeatherMap'ten gelen tahmin listesi.
     * @returns {Object|null} {description: string, icon: string} veya bulunamazsa null.
     */
    function getWeatherDescriptionForEventDateJS(eventDateStr, forecastList) {
        // Conditional logging for May 18th or any date causing issues
        const tempEventDateForLog = eventDateStr ? new Date(eventDateStr) : null;
        if (!forecastList || forecastList.length === 0 || !eventDateStr) {
            return null;
        }

        try {
            const eventDate = new Date(eventDateStr);
            // Conditional logging for May 18th, 2025
            const isProblemDate = eventDate.getDate() === 18 && (eventDate.getMonth() + 1) === 5 && eventDate.getFullYear() === 2025;

            let bestForecast = null;
            let firstAvailableForecast = null;

            for (const forecast of forecastList) {
                const forecastDate = new Date(forecast.dt * 1000); // dt, saniye cinsinden UNIX timestamp

                if (eventDate.getFullYear() === forecastDate.getFullYear() &&
                    eventDate.getMonth() === forecastDate.getMonth() &&
                    eventDate.getDate() === forecastDate.getDate()) {
                    
                    // O güne ait ve açıklaması olan ilk tahmini sakla
                    if (!firstAvailableForecast && forecast.weather && forecast.weather[0] && forecast.weather[0].description) {
                        firstAvailableForecast = {
                            description: forecast.weather[0].description,
                            icon: forecast.weather[0].icon || null
                        };
                    }

                    // Öğlen saatlerindeki (12:00 - 15:00 arası) ve açıklaması olan tahmini önceliklendir
                    if (forecastDate.getHours() >= 12 && forecastDate.getHours() < 15) {
                        if (forecast.weather && forecast.weather[0] && forecast.weather[0].description) {
                            bestForecast = {
                                description: forecast.weather[0].description,
                                icon: forecast.weather[0].icon || null
                            };
                            break; // İdeal tahmini bulduk, döngüden çık
                        }
                    }
                }
            }
            
            // Önce ideal (öğlen) tahmini, sonra o güne ait açıklaması olan ilk tahmini, hiçbiri yoksa null döndür
            if (bestForecast) return bestForecast;
            if (firstAvailableForecast) return firstAvailableForecast;

        } catch (e) {
            console.error("Error processing weather for event:", eventDateStr, e);
            return null;
        }
        return null; 
    }

    document.addEventListener('DOMContentLoaded', async function() {
        const weatherLoading = document.getElementById('weather-loading');
        const weatherContent = document.getElementById('weather-content');
        const weatherWidget = document.getElementById('weather-widget');
        
        fetch('/api/weather')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data && data.name && data.main && data.weather && data.wind) {
                    document.getElementById('weather-location').textContent = data.name;
                    document.getElementById('weather-temp').textContent = `${Math.round(data.main.temp)}°C`;
                    document.getElementById('weather-desc').textContent = data.weather[0].description;
                    document.getElementById('weather-humidity').textContent = `${data.main.humidity}%`;
                    document.getElementById('weather-wind').textContent = `${Math.round(data.wind.speed)} km/s`;
                    document.getElementById('weather-icon').src = `https://openweathermap.org/img/wn/${data.weather[0].icon}@4x.png`;

                    if (weatherLoading) weatherLoading.style.display = 'none';
                    if (weatherContent) weatherContent.classList.remove('hidden');
                } else {
                    throw new Error('Incomplete weather data received');
                }
            })
            .catch(error => {
                console.error('Hava durumu verileri alınamadı veya işlenemedi:', error);
                if (weatherWidget) {
                    weatherWidget.innerHTML = '<p class="text-red-400 text-center p-4">Hava durumu bilgileri şu anda yüklenemiyor.</p>';
                }
            });

        const eventsLoading = document.getElementById('events-loading');
        const eventsList = document.getElementById('events-list');
        const noEventsMessage = document.getElementById('no-events');
        const eventTypeFiltersContainer = document.getElementById('event-type-filters');
        let allLocalEvents = [];
        let allTicketmasterEvents = [];
        let activeFilterButton = null;

        async function fetchEventTypesAndCreateButtons() {
            try {
                const response = await fetch('/api/event-types').then(res => res.json());
                if (response.status === 'success' && response.data) {
                    const types = response.data;
                    for (const typeKey in types) {
                        const button = document.createElement('button');
                        button.dataset.type = typeKey;
                        button.textContent = types[typeKey];
                        button.className = 'event-filter-btn bg-slate-700 hover:bg-slate-600 text-gray-200 font-semibold py-2 px-5 rounded-lg shadow-sm transition-all duration-150 ease-in-out transform hover:scale-105';
                        button.addEventListener('click', () => filterEventsByType(typeKey, button));
                        eventTypeFiltersContainer.appendChild(button);
                    }
                }
            } catch (error) {
                console.error('Etkinlik türleri çekilirken hata:', error);
            }
        }

        function displayEvents(eventsToDisplay) {
            eventsList.innerHTML = '';
            noEventsMessage.style.display = 'none';

            if (!eventsToDisplay || eventsToDisplay.length === 0) {
                noEventsMessage.style.display = 'block';
                eventsList.style.opacity = '1';
                return;
            }

            eventsToDisplay.forEach(event => {
                const eventDate = new Date(event.date);
                const formattedDate = eventDate.toLocaleDateString('tr-TR', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                const typeDisplay = event.type_display_name || (event.type ? event.type.charAt(0).toUpperCase() + event.type.slice(1) : 'Bilinmiyor');

                // Hava durumu veya etkinlik açıklaması için metin
                let descriptionText = '';

                const weatherDetails = getWeatherDescriptionForEventDateJS(event.date, weatherForecastList);

                if (weatherDetails && weatherDetails.description) {
                    let iconHtml = '';
                    if (weatherDetails.icon) {
                        // @2x kullanarak daha büyük ve net bir ikon alalım. Boyutunu class ile ayarlayabiliriz.
                        iconHtml = `<img src="https://openweathermap.org/img/wn/${weatherDetails.icon}@2x.png" alt="" class="inline-block h-6 w-6 mr-1 align-middle">`;
                    }
                    descriptionText = `${iconHtml}${weatherDetails.description.charAt(0).toUpperCase() + weatherDetails.description.slice(1)}`;
                } else if (event.description && typeof event.description === 'string' && event.description.trim() !== '') {
                    descriptionText = event.description;
                } else {
                    descriptionText = 'Açıklama mevcut değil.';
                }

                // Fiyat ve Kontenjan Bilgisi
                let priceDisplay = '<span class="text-sm text-gray-400">Fiyat: Belirlenmedi</span>'; // Varsayılan
                if (event.source === 'ticketmaster') {
                    let childPrice = null;
                    let adultPrice = null;
                    if (event.custom_data) {
                        if (event.custom_data.child_price !== undefined && event.custom_data.child_price !== null && event.custom_data.child_price > 0) {
                            childPrice = event.custom_data.child_price;
                        }
                        if (event.custom_data.adult_price !== undefined && event.custom_data.adult_price !== null && event.custom_data.adult_price > 0) {
                            adultPrice = event.custom_data.adult_price;
                        }
                    }
                    let mainPrice = event.display_price !== null ? event.display_price : (event.price !== null ? event.price : 0);
                    if (adultPrice === null) adultPrice = mainPrice;
                    if (childPrice === null) childPrice = Math.round(mainPrice * 0.5 * 100) / 100;
                    if (adultPrice > 0 || childPrice > 0) {
                        priceDisplay = `<span class="font-semibold text-emerald-400">Çocuk: ${parseFloat(childPrice).toFixed(2)} TL<br>Tam: ${parseFloat(adultPrice).toFixed(2)} TL</span>`;
                    }
                } else { // Yerel etkinlik
                    if (event.price !== null) {
                        priceDisplay = `<span class="font-semibold text-emerald-400">${parseFloat(event.price).toFixed(2)} TL</span>`;
                    }
                }

                let capacityDisplay = '<span class="text-sm text-gray-400">Kontenjan: Belirlenmedi</span>'; // Varsayılan ve TM için API durumu
                if (event.source === 'ticketmaster') {
                    if (event.custom_data && event.custom_data.override_capacity !== undefined && event.custom_data.override_capacity !== null) {
                        if (parseInt(event.custom_data.override_capacity) === 0) {
                            capacityDisplay = `<span class="font-semibold text-red-500">Biletler tükenmiştir</span>`;
                        } else {
                            capacityDisplay = `<span class="font-semibold text-sky-400">${event.custom_data.override_capacity}</span>`;
                        }
                    }
                } else { // Yerel etkinlik
                    if (event.available_tickets !== null && event.available_tickets !== undefined) {
                        if (parseInt(event.available_tickets) === 0) {
                            capacityDisplay = `<span class="font-semibold text-red-500">Biletler tükenmiştir</span>`;
                        } else {
                            capacityDisplay = `<span class="font-semibold text-sky-400">${event.available_tickets}</span>`;
                        }
                    }
                }

                // Hava durumu ve planlanabilirlik bilgisi (frontendde hesapla, sadece uygun/riskli/bilgi yok yazısı)
                let weatherStatusDisplay = '';
                if (weatherDetails && weatherDetails.description) {
                    // OpenWeatherMap weather kodunu ve sıcaklığı bul
                    let forecast = null;
                    for (const f of weatherForecastList) {
                        const forecastDate = new Date(f.dt * 1000);
                        if (forecastDate.getFullYear() === eventDate.getFullYear() &&
                            forecastDate.getMonth() === eventDate.getMonth() &&
                            forecastDate.getDate() === eventDate.getDate()) {
                            forecast = f;
                            break;
                        }
                    }
                    if (forecast && forecast.weather && forecast.weather[0]) {
                        const weatherCode = forecast.weather[0].id;
                        const temperature = forecast.main.temp;
                        if ((weatherCode >= 200 && weatherCode < 600) || temperature > 30 || temperature < 5) {
                            weatherStatusDisplay = `<div class=\"text-yellow-400 font-semibold mb-1\">Etkinlik hava koşulları nedeniyle riskli olabilir</div>`;
                        } else {
                            weatherStatusDisplay = `<div class=\"text-green-400 font-semibold mb-1\">Etkinlik planlanabilir</div>`;
                        }
                    } else {
                        weatherStatusDisplay = `<div class=\"text-gray-400 font-semibold mb-1\">Hava durumu bilgisi alınamadı</div>`;
                    }
                } else {
                    weatherStatusDisplay = `<div class=\"text-gray-400 font-semibold mb-1\">Hava durumu bilgisi alınamadı</div>`;
                }

                let addToCartButton = '';
                const isTicketmasterEvent = event.source === 'ticketmaster';
                const isSoldOut = isTicketmasterEvent
                    ? (event.custom_data && parseInt(event.custom_data.override_capacity) === 0)
                    : (parseInt(event.available_tickets) === 0);

                if (isSoldOut) {
                    addToCartButton = `
                        <button class="w-full bg-gray-400 text-white font-semibold py-2 px-4 rounded-lg text-sm text-center" disabled>
                            Biletler tükenmiştir
                        </button>
                    `;
                } else if (isTicketmasterEvent) {
                    const ticketmasterEventId = event.id;
                    const ticketmasterEventName = event.title || 'Ticketmaster Etkinliği';
                    const ticketmasterEventPrice = event.display_price !== null ? event.display_price : 0;
                    const ticketmasterImage = event.image_url || '';

                    addToCartButton = `
                        <form action="${cartAddUrl}" method="POST" class="mt-3 add-to-cart-form">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="event_id" value="tm_${ticketmasterEventId}">
                            <input type="hidden" name="event_name" value="${ticketmasterEventName}">
                            <input type="hidden" name="event_price" value="${ticketmasterEventPrice}">
                            <input type="hidden" name="event_image" value="${ticketmasterImage}">
                            <input type="hidden" name="is_ticketmaster" value="true">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-lg text-sm text-center transition-all duration-200 shadow-md hover:shadow-lg">
                                Sepete Ekle
                            </button>
                        </form>
                    `;
                } else {
                    addToCartButton = `
                        <form action="${cartAddUrl}" method="POST" class="mt-3 add-to-cart-form">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="event_id" value="${event.id}">
                            <input type="hidden" name="event_name" value="${event.title || 'Yerel Etkinlik'}">
                            <input type="hidden" name="event_price" value="${event.price}">
                            <input type="hidden" name="event_image" value="${event.image_url || ''}">
                            <input type="hidden" name="is_ticketmaster" value="false">
                            <input type="hidden" name="quantity" value="1"> 
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg text-sm text-center transition-all duration-200 shadow-md hover:shadow-lg">
                                Sepete Ekle
                            </button>
                        </form>
                    `;
                }

                // Kart HTML'i önceki (daha sade) yapısına döndürülüyor.
                // event.description direkt kullanılıyor, ya da 'Açıklama mevcut değil' yazısı gösteriliyor.
                const card = `
                    <div class="bg-slate-800 shadow-xl rounded-lg overflow-hidden transform hover:scale-[1.03] transition-transform duration-300 ease-in-out flex flex-col h-full">
                        ${event.image_url ? `<img src="${event.image_url}" alt="${event.title}" class="w-full h-56 object-cover">` : '<div class="w-full h-56 bg-slate-700 flex items-center justify-center"><span class="text-slate-400 text-lg">Resim Yok</span></div>'}
                        <div class="p-5 flex flex-col flex-grow">
                            <h3 class="text-2xl font-semibold text-gray-100 mb-3">${event.title}</h3>
                            <p class="text-sm text-purple-300 mb-1">Tür: ${typeDisplay}</p>
                            <p class="text-sm text-sky-300 mb-3">Tarih: ${formattedDate}</p>
                            <p class="text-gray-300 text-sm mb-4 leading-relaxed flex-grow h-20 overflow-hidden">${descriptionText}</p>
                            <div class="mt-auto">
                                {{-- Fiyat ve Kontenjan Bilgisi --}}
                                <div class="mb-3 px-1 pt-2 border-t border-slate-700/50">
                                    ${weatherStatusDisplay}
                                    <div class="flex justify-between items-center text-sm">
                                        <div class="flex items-center text-gray-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h10a3 3 0 013 3v5a.997.997 0 01-.293-.707zM16 5a1 1 0 00-1-1H5a1 1 0 00-1 1v4.586l6 6 6-6V5z" clip-rule="evenodd" />
                                                <path fill-rule="evenodd" d="M10 11a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                            </svg>
                                            ${priceDisplay}
                                        </div>
                                        <div class="flex items-center text-gray-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5 text-sky-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0115 11h1c.414 0 .79.322.932.72A10.002 10.002 0 0020 12c0 .339-.025.672-.07 1H4.07A10.002 10.002 0 004 12c0-.339.025.672.07-1h1a5 5 0 013.456 1.67A6.97 6.97 0 007 16c0 .34.024.673.07 1h5.86z" />
                                            </svg>
                                            ${capacityDisplay}
                                        </div>
                                    </div>
                                </div>
                                ${event.url ? `<a href="${event.url}" target="_blank" class="inline-block w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold py-2 px-6 rounded-lg text-sm text-center transition-all duration-200 shadow-md hover:shadow-lg">Detaylar</a>` : ''}
                                ${addToCartButton} 
                            </div>
                        </div>
                    </div>
                `;
                eventsList.innerHTML += card;
            });
            eventsList.style.opacity = '1';
        }

        function filterEventsByType(typeKey, clickedButton) {
            if (activeFilterButton) {
                activeFilterButton.classList.remove('bg-purple-600', 'text-white');
                activeFilterButton.classList.add('bg-slate-700', 'text-gray-200');
            }
            clickedButton.classList.add('bg-purple-600', 'text-white');
            clickedButton.classList.remove('bg-slate-700', 'text-gray-200');
            activeFilterButton = clickedButton;

            let combinedEvents = [...allLocalEvents, ...allTicketmasterEvents];
            let filteredEvents = [];

            if (typeKey === 'all') {
                filteredEvents = combinedEvents;
            } else {
                filteredEvents = combinedEvents.filter(event => {
                    if (event.source === 'ticketmaster') {
                        return event.type_key === typeKey;
                    } else { 
                        return event.type === typeKey;
                    }
                });
            }
            filteredEvents.sort((a, b) => new Date(b.date) - new Date(a.date));
            displayEvents(filteredEvents);
        }

        async function fetchAndDisplayEvents() {
            if (eventsLoading) eventsLoading.style.display = 'flex';
            if (eventsList) eventsList.style.opacity = '0';
            if (noEventsMessage) noEventsMessage.style.display = 'none';

            try {
                // Yerel etkinlikleri çek
                const localEventsResponse = await fetch('/api/events').then(res => res.json());
                allLocalEvents = localEventsResponse.data && Array.isArray(localEventsResponse.data) ? 
                                 localEventsResponse.data.map(event => ({...event, source: 'local'})) : [];
                
                // Ticketmaster etkinliklerini çek
                const ticketmasterEventsResponse = await fetch('/api/ticketmaster-events?city=İstanbul').then(res => res.json());
                allTicketmasterEvents = ticketmasterEventsResponse.data && Array.isArray(ticketmasterEventsResponse.data) ? 
                                        ticketmasterEventsResponse.data.map(event => {
                                            let tmTypeKey = 'other'; 
                                            let tmTypeDisplayName = 'Diğer';

                                            if (event.classifications && event.classifications[0] && event.classifications[0].segment) {
                                                const segmentName = event.classifications[0].segment.name.toLowerCase();
                                                if (segmentName.includes('music')) { 
                                                    tmTypeKey = 'concert'; 
                                                    tmTypeDisplayName = 'Konser';
                                                } else if (segmentName.includes('sport')) { 
                                                    tmTypeKey = 'sports'; 
                                                    tmTypeDisplayName = 'Spor';
                                                } else if (segmentName.includes('arts & theatre') || segmentName.includes('theatre') || segmentName.includes('art')) {
                                                    tmTypeKey = 'theatre'; 
                                                    tmTypeDisplayName = 'Tiyatro';
                                                } else if (segmentName.includes('comedy')) {
                                                    tmTypeKey = 'comedy';
                                                    tmTypeDisplayName = 'Komedi';
                                                }
                                                
                                                if (tmTypeDisplayName === 'Diğer' && event.classifications[0].genre && event.classifications[0].genre.name) {
                                                     tmTypeDisplayName = event.classifications[0].genre.name;
                                                }
                                            }
                                            
                                            const mappedEventObject = {
                                                id: event.id,
                                                title: event.name,
                                                description: event.description || (event.info || (event.pleaseNote || '')),
                                                date: event.dates.start.dateTime,
                                                image_url: event.images && event.images.length > 0 ? event.images.find(img => img.ratio === "16_9" && img.width > 600)?.url || event.images[0].url : '',
                                                url: event.url,
                                                type_key: tmTypeKey, 
                                                type_display_name: tmTypeDisplayName,
                                                source: 'ticketmaster',
                                                display_price: event.display_price, 
                                                custom_data: event.custom_data 
                                            };
                                            return mappedEventObject;
                                        }) : [];

                let combinedEvents = [...allLocalEvents, ...allTicketmasterEvents];
                // Tarihe göre sıralama (en yeniden en eskiye)
                combinedEvents.sort((a, b) => new Date(b.date) - new Date(a.date));

                displayEvents(combinedEvents);

            } catch (error) {
                console.error('Etkinlikler yüklenirken bir hata oluştu:', error);
                if (noEventsMessage) {
                    noEventsMessage.textContent = 'Etkinlikler yüklenemedi. Lütfen daha sonra tekrar deneyin.';
                    noEventsMessage.style.display = 'block';
                }
            } finally {
                if (eventsLoading) eventsLoading.style.display = 'none';
                if (eventsList) eventsList.style.opacity = '1'; // Ensure list is visible even if empty for noEventsMessage
            }
        }

        const announcementsLoading = document.getElementById('announcements-loading');
        const announcementsList = document.getElementById('announcements-list');
        
        fetch('/api/announcements')
            .then(res => res.json())
            .then(data => {
                const announcements = data.data || data;
                if(announcementsList) announcementsList.innerHTML = '';
                announcements.forEach((announcement, index) => {
                    const delay = index * 100;
                    const announcementCard = `
                        <div class="transform transition-all duration-300 hover:scale-[1.02] opacity-0 bg-slate-800 rounded-xl shadow-xl overflow-hidden"
                             style="animation: fadeIn 0.6s ease-out ${delay}ms forwards">
                            <div class="p-6">
                                <h4 class="text-xl font-semibold text-gray-100 mb-2 hover:text-sky-400 transition-colors duration-300">
                                    ${announcement.title}
                                </h4>
                                <p class="text-gray-400 text-sm mb-4 leading-relaxed">
                                    ${announcement.content || 'İçerik mevcut değil.'}
                                </p>
                                <div class="flex items-center text-xs text-sky-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    ${new Date(announcement.created_at).toLocaleString('tr-TR', {
                                        day: 'numeric',
                                        month: 'long',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    })}
                                </div>
                            </div>
                        </div>
                    `;
                    if(announcementsList) announcementsList.innerHTML += announcementCard;
                });
                if(announcementsLoading) announcementsLoading.style.display = 'none';
                if(announcementsList) announcementsList.style.opacity = '1';
            })
            .catch(error => {
                console.error('Duyurular çekilirken hata:', error);
                if(announcementsList) announcementsList.innerHTML = '<p class="text-red-400 text-center p-4">Duyurular şu anda yüklenemiyor.</p>';
                if(announcementsLoading) announcementsLoading.style.display = 'none';
                if(announcementsList) announcementsList.style.opacity = '1';
            });

        await fetchEventTypesAndCreateButtons();

        const allButton = eventTypeFiltersContainer.querySelector('button[data-type="all"]');
        if (allButton) {
            if (!activeFilterButton) {
                activeFilterButton = allButton;
            }
            if (!allButton.getAttribute('listener-attached')) {
                allButton.addEventListener('click', () => filterEventsByType('all', allButton));
                allButton.setAttribute('listener-attached', 'true');
            }
        }
        
        fetchAndDisplayEvents();

        // AJAX ile Sepete Ekleme İşlevi
        const eventsListContainer = document.getElementById('events-list');
        if (eventsListContainer) {
            eventsListContainer.addEventListener('submit', async function(event) {
                if (event.target.classList.contains('add-to-cart-form')) {
                    event.preventDefault(); // Sayfanın yeniden yüklenmesini engelle

                    const form = event.target;
                    const formData = new FormData(form);
                    const submitButton = form.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton.innerHTML;

                    submitButton.innerHTML = 'Ekleniyor...';
                    submitButton.disabled = true;

                    try {
                        const response = await fetch(cartAddUrl, { // cartAddUrl global olarak tanımlı
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken, // csrfToken global olarak tanımlı
                                'Accept': 'application/json',
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            const desktopCounter = document.getElementById('cart-item-count-desktop');
                            const mobileCounter = document.getElementById('cart-item-count-mobile');
                            if (desktopCounter) desktopCounter.textContent = data.cartItemCount;
                            if (mobileCounter) mobileCounter.textContent = data.cartItemCount;
                            
                            alert(data.message || 'Etkinlik sepete başarıyla eklendi!');
                        } else {
                            alert(data.message || 'Sepete eklenirken bir sorun oluştu. Lütfen tekrar deneyin.');
                        }
                    } catch (error) {
                        console.error('Sepete ekleme AJAX hatası:', error);
                        alert('Sepete eklenirken bir ağ hatası oluştu. Lütfen internet bağlantınızı kontrol edin veya daha sonra tekrar deneyin.');
                    } finally {
                        submitButton.innerHTML = originalButtonText;
                        submitButton.disabled = false;
                    }
                }
            });
        }
    });
</script>
@endsection