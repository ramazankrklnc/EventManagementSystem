@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded shadow mt-8">
    <h1 class="text-2xl font-bold mb-4">Duyurular</h1>
    <table class="min-w-full bg-white border mt-4">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Başlık</th>
                <th class="py-2 px-4 border-b">Tarih</th>
            </tr>
        </thead>
        <tbody>
            @foreach($announcements as $announcement)
                <tr>
                    <td class="py-2 px-4 border-b">
                        <a href="#" class="text-blue-600 hover:underline">{{ $announcement['title'] }}</a>
                    </td>
                    <td class="py-2 px-4 border-b">{{ $announcement['created_at']->format('d.m.Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if(empty($announcements))
        <p class="mt-4 text-gray-500">Henüz duyuru yok.</p>
    @endif
</div>
@endsection 