@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow mt-8">
    <h1 class="text-2xl font-bold mb-4">{{ $announcement->title }}</h1>
    <div class="mb-4 text-gray-600 text-sm">{{ $announcement->created_at->format('d.m.Y H:i') }}</div>
    <div class="mb-6">{!! nl2br(e($announcement->content)) !!}</div>
    <a href="{{ route('admin.announcements.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Geri Dön</a>
</div>
@endsection 