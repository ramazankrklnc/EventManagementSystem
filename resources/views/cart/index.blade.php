@extends('layouts.app')

@section('title', 'Etkinlik Sepetim')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Etkinlik Sepetim') }}
    </h2>
@endsection

@section('content')
    <div class="py-12 bg-gray-900 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8">

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-4 border-b border-gray-700">
                        <h3 class="text-2xl font-semibold text-gray-100 mb-4 sm:mb-0">Etkinlik Sepetiniz</h3>
                        <a href="{{ route('home') }}" class="text-sm font-medium text-indigo-400 hover:text-indigo-300">
                            &larr; Etkinliklere Geri Dön
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-700 border-l-4 border-green-500 text-green-100 rounded-md text-sm">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 p-3 bg-red-700 border-l-4 border-red-500 text-red-100 rounded-md text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Türkçe Açıklama: Etkinlik Sepetim sayfası. Kullanıcının sepete eklediği etkinlik biletlerini, adetlerini ve toplam tutarı gösterir. --}}
                    {{-- Kullanıcı bilet adetlerini güncelleyebilir, sepetten çıkarabilir ve ödeme adımına geçebilir. --}}

                    @if(isset($cartItems) && count($cartItems) > 0)
                        <div class="space-y-6 mb-8">
                            @foreach($cartItems as $item)
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-gray-700 rounded-lg shadow">
                                    <div class="flex items-center mb-4 sm:mb-0 flex-grow">
                                        <img src="{{ $item['image_url'] ?? asset('images/placeholder-event.jpg') }}" alt="{{ $item['name'] }}" class="w-20 h-20 object-cover rounded-md mr-4 sm:mr-6">
                                        <div class="flex-grow">
                                            <h4 class="text-lg font-medium text-gray-100">{{ $item['name'] }}</h4>
                                            <p class="text-sm text-gray-300">Tam Bilet: {{ number_format($item['adult_price'] ?? $item['price'] ?? 0, 2) }} TL</p>
                                            <p class="text-sm text-gray-300">Çocuk Bileti: {{ number_format($item['child_price'] ?? $item['price'] ?? 0, 2) }} TL</p>
                                            <form action="{{ route('cart.update') }}" method="POST" class="mt-2 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                                @csrf
                                                <input type="hidden" name="event_id" value="{{ $item['id'] }}">
                                                <div class="flex items-center">
                                                    <label for="adult_quantity-{{ $item['id'] }}" class="text-xs text-gray-400 mr-2">Tam Bilet Adet:</label>
                                                    <input type="number" id="adult_quantity-{{ $item['id'] }}" name="ticket_types[adult]" value="{{ $item['ticket_types']['adult'] ?? 0 }}" class="w-16 text-sm rounded border-gray-600 bg-gray-800 text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 h-8">
                                                </div>
                                                <div class="flex items-center">
                                                    <label for="child_quantity-{{ $item['id'] }}" class="text-xs text-gray-400 mr-2">Çocuk Bileti Adet:</label>
                                                    <input type="number" id="child_quantity-{{ $item['id'] }}" name="ticket_types[child]" value="{{ $item['ticket_types']['child'] ?? 0 }}" class="w-16 text-sm rounded border-gray-600 bg-gray-800 text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 h-8">
                                                </div>
                                                <button type="submit" class="px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs rounded-md h-8">Güncelle</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="text-right mt-4 sm:mt-0 flex flex-col items-end">
                                        @php
                                            $adultPrice = $item['adult_price'] ?? $item['price'] ?? 0;
                                            $childPrice = $item['child_price'] ?? $item['price'] ?? 0;
                                            $adultTotal = $adultPrice * ($item['ticket_types']['adult'] ?? 0);
                                            $childTotal = $childPrice * ($item['ticket_types']['child'] ?? 0);
                                            $itemTotal = $adultTotal + $childTotal;
                                        @endphp
                                        <p class="text-lg font-semibold text-gray-100 mb-2">{{ number_format($itemTotal, 2) }} TL</p>
                                        <form action="{{ route('cart.remove') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="event_id" value="{{ $item['id'] }}">
                                            <button type="submit" class="text-sm font-medium text-red-400 hover:text-red-300">
                                                Sepetten Çıkar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="bg-gray-700 p-6 rounded-lg shadow">
                            <h4 class="text-xl font-semibold text-gray-100 mb-4">Bilet Özeti</h4>
                            <div class="space-y-2 mb-6">
                                <div class="flex justify-between">
                                    <span class="text-gray-300">Ara Toplam:</span>
                                    <span class="text-gray-100 font-medium">{{ number_format($totalPrice, 2) }} TL</span>
                                </div>
                                <div class="flex justify-between text-lg font-semibold pt-2 border-t border-gray-600">
                                    <span class="text-gray-100">Genel Toplam:</span>
                                    <span class="text-indigo-400">{{ number_format($totalPrice, 2) }} TL</span>
                                </div>
                            </div>

                            <div class="mb-6">
                                <h5 class="text-md font-medium text-gray-100 mb-2">Ödeme Yöntemi</h5>
                                <div class="space-y-3">
                                    <label class="flex items-center p-3 bg-gray-600 rounded-md shadow-sm hover:bg-gray-500 cursor-pointer transition duration-150 ease-in-out">
                                        <input type="radio" name="payment_method" value="credit_card" class="form-radio h-4 w-4 text-indigo-500 border-gray-500 focus:ring-indigo-500 focus:ring-offset-0 bg-gray-700" checked>
                                        <span class="ml-3 text-sm text-gray-200">Kredi / Banka Kartı</span>
                                    </label>
                                </div>
                            </div>

                            <form action="{{ route('cart.checkout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                    Ödemeye Geç
                                </button>
                            </form>
                        </div>

                    @else
                        <div class="text-center py-16">
                            <svg class="mx-auto h-16 w-16 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-3.75h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zm2.25-4.5h.008v.008H14.25v-.008zm0 2.25h.008v.008H14.25V15zm2.25-2.25h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5v-.008z" />
                            </svg>
                            <h3 class="mt-4 text-xl font-semibold text-gray-100">Etkinlik Sepetiniz Henüz Boş</h3>
                            <p class="mt-2 text-sm text-gray-400">Beğendiğiniz etkinlikleri sepetinize ekleyerek bilet alım işlemine başlayabilirsiniz.</p>
                            <div class="mt-8">
                                <a href="{{ route('home') }}" 
                                   class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v11.494m0 0A7.5 7.5 0 1012 6.253zM12 6.253V3M12 17.747V21M6.253 12H3m14.747 0H21" />
                                      </svg>
                                    Etkinlikleri Keşfet
                                </a>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection 