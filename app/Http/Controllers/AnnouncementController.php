<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        // Örnek duyurular
        $announcements = [
            [
                'title' => 'Duyuru 1',
                'content' => 'Birinci duyuru içeriği',
                'created_at' => now(),
            ],
            [
                'title' => 'Duyuru 2',
                'content' => 'İkinci duyuru içeriği',
                'created_at' => now()->subDay(),
            ],
        ];
        return view('announcements.index', compact('announcements'));
    }
}
