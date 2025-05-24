@extends('layouts.admin') {{-- Admin layout'u kullanılıyor varsayalım --}}

@section('title', 'Yeni Etkinlik Ekle')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-700 mb-6">Yeni Etkinlik Ekle</h1>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
            @include('admin.events._form')
        </form>
    </div>
</div>
@endsection 