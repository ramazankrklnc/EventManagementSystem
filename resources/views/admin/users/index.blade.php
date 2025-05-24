@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
                <svg class="h-8 w-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <div class="ml-4">
                    <h2 class="text-2xl font-semibold text-white">Kullanıcıları Yönet</h2>
                    <p class="text-gray-400 mt-1">Kullanıcı hesaplarını yönetin ve yetkilendirin</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-900 border-l-4 border-green-500 text-green-100 rounded-lg">
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

        <div class="bg-gray-800 shadow-xl rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    E-posta
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Kullanıcı Rolü
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Onay Durumu
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($user->email) }}&background=0D8ABC&color=fff" alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-white">
                                                    {{ $user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->role === 'admin')
                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-900 text-purple-200">
                                                Yönetici
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-900 text-blue-200">
                                                Normal Kullanıcı
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->approved)
                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-900 text-green-200">
                                                Onaylandı
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-900 text-yellow-200">
                                                Onay Bekliyor
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-3">
                                            @if(!$user->approved && $user->role !== 'admin')
                                                <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" 
                                                        onclick="return confirm('Kullanıcıyı onaylamak istediğinize emin misiniz?')"
                                                        class="text-green-400 hover:text-green-300 focus:outline-none text-xs transition">
                                                        Onayla
                                                    </button>
                                                </form>
                                            @endif
                                            @if($user->role !== 'admin')
                                                <form action="{{ route('admin.users.makeAdmin', $user) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" 
                                                        onclick="return confirm('Kullanıcıyı yönetici yapmak istediğinize emin misiniz?')"
                                                        class="text-indigo-400 hover:text-indigo-300 focus:outline-none text-xs transition">
                                                        Yönetici Yap
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                    onclick="return confirm('Kullanıcıyı silmek istediğinize emin misiniz?')"
                                                    class="text-red-400 hover:text-red-300 focus:outline-none text-xs transition">
                                                    Sil
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 