@extends('layouts.app')

@section('title', 'Profil Düzenle')

@section('content')
    <header class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profil Bilgilerim
                </h2>
                <span class="text-sm text-gray-500">Son güncelleme: {{ auth()->user()->updated_at->format('d.m.Y H:i') }}</span>
            </div>
        </div>
    </header>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sol Kolon: Profil Bilgileri ve Şifre -->
                <div class="space-y-6">
                    <!-- Profil Bilgileri -->
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Şifre Güncelleme -->
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                <!-- Sağ Kolon: Hesap Durumu ve Tehlikeli Bölge -->
                <div class="space-y-6">
                    <!-- Hesap Özeti -->
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Hesap Özeti
                            </h3>
                            <div class="mt-6 space-y-4">
                                <!-- Kullanıcı Rolü -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Kullanıcı Rolü</span>
                                    <span class="px-3 py-1 text-sm rounded-full {{ auth()->user()->isAdmin() ? 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100' : 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' }}">
                                        {{ auth()->user()->isAdmin() ? 'Yönetici' : 'Normal Kullanıcı' }}
                                    </span>
                                </div>
                                <!-- Hesap Durumu -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Hesap Durumu</span>
                                    <span class="px-3 py-1 text-sm rounded-full {{ auth()->user()->approved ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' }}">
                                        {{ auth()->user()->approved ? 'Onaylanmış' : 'Onay Bekliyor' }}
                                    </span>
                                </div>
                                <!-- Şifre Durumu -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Şifre Durumu</span>
                                    <span class="px-3 py-1 text-sm rounded-full {{ auth()->user()->password_changed ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                        {{ auth()->user()->password_changed ? 'Güncel' : 'Güncelleme Gerekli' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hesap Silme -->
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
