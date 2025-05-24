@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-900 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
                <span class="text-xl font-medium text-gray-300">Hoş geldin, {{ Auth::user()->name }}</span>
            </div>
        </div>

        <!-- Welcome Message -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold text-white mb-2">Yönetici Paneli</h2>
            <p class="text-gray-400">Buradan etkinlikleri, kullanıcıları ve duyuruları kolayca yönetebilirsiniz.</p>
        </div>

        <!-- Management Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Etkinlikleri Yönet Card -->
            <a href="{{ route('admin.events.index') }}" class="block">
                <div class="bg-blue-900/30 hover:bg-blue-900/40 rounded-lg shadow-lg p-6 transition duration-300">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="h-12 w-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white text-center mb-2">Etkinlikleri Yönet</h3>
                    <p class="text-gray-400 text-center text-sm">Etkinlik ekle, düzenle, yayına al veya kaldır</p>
                </div>
            </a>

            <!-- Kullanıcıları Yönet Card -->
            <a href="{{ route('admin.users.index') }}" class="block">
                <div class="bg-green-900/30 hover:bg-green-900/40 rounded-lg shadow-lg p-6 transition duration-300">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white text-center mb-2">Kullanıcıları Yönet</h3>
                    <p class="text-gray-400 text-center text-sm">Kullanıcı onayla, sil veya düzenle</p>
                </div>
            </a>

            <!-- Duyuruları Yönet Card -->
            <a href="{{ route('admin.announcements.index') }}" class="block">
                <div class="bg-yellow-900/30 hover:bg-yellow-900/40 rounded-lg shadow-lg p-6 transition duration-300">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="h-12 w-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white text-center mb-2">Duyuruları Yönet</h3>
                    <p class="text-gray-400 text-center text-sm">Yeni duyuru ekle, düzenle veya sil</p>
                </div>
            </a>
        </div>

        <!-- Stats Section -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Toplam Etkinlik</p>
                        <p class="text-2xl font-semibold text-white">{{ $totalPublishedEvents }}</p>
                    </div>
                    <div class="p-3 bg-blue-900/30 rounded-full">
                        <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Toplam Kullanıcı</p>
                        <p class="text-2xl font-semibold text-white">{{ \App\Models\User::count() }}</p>
                    </div>
                    <div class="p-3 bg-green-900/30 rounded-full">
                        <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Aktif Duyurular</p>
                        <p class="text-2xl font-semibold text-white">{{ \App\Models\Announcement::where('status', 'active')->count() }}</p>
                    </div>
                    <div class="p-3 bg-yellow-900/30 rounded-full">
                        <svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 