@extends('layouts.admin') {{-- Admin layout'u kullanılıyor varsayalım --}}

@section('title', 'Etkinliği Düzenle: ' . $event->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-700 mb-6">Etkinliği Düzenle: <span class="font-normal">{{ $event->title }}</span></h1>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @include('admin.events._form', ['event' => $event])
        </form>
    </div>
</div>
@endsection 