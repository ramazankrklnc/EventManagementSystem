<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'title' => 'Rock Festivali 2024',
                'description' => 'Yılın en büyük rock festivali! Birçok ünlü sanatçı sahne alacak.',
                'type' => 'concert',
                'event_date' => Carbon::now()->addDays(10),
                'location' => 'Ankara Arena',
                'price' => 350.00,
                'available_tickets' => 1000,
                'weather_dependent' => true,
                'categories' => ['music', 'rock', 'festival'],
                'image_url' => 'https://example.com/images/rock-festival.jpg'
            ],
            [
                'title' => 'Modern Sanat Sergisi',
                'description' => 'Çağdaş sanatçıların eserlerinin yer aldığı büyük sergi.',
                'type' => 'exhibition',
                'event_date' => Carbon::now()->addDays(5),
                'location' => 'Ankara Modern Sanat Müzesi',
                'price' => 100.00,
                'available_tickets' => 500,
                'weather_dependent' => false,
                'categories' => ['art', 'modern', 'culture'],
                'image_url' => 'https://example.com/images/art-exhibition.jpg'
            ],
            [
                'title' => 'Süper Lig Derbi Maçı',
                'description' => 'Sezonun en önemli derbi karşılaşması!',
                'type' => 'sports',
                'event_date' => Carbon::now()->addDays(15),
                'location' => 'Ankara 19 Mayıs Stadyumu',
                'price' => 250.00,
                'available_tickets' => 2000,
                'weather_dependent' => true,
                'categories' => ['sports', 'football', 'derby'],
                'image_url' => 'https://example.com/images/derby-match.jpg'
            ],
            [
                'title' => 'Romeo ve Juliet',
                'description' => 'Shakespearein ölümsüz aşk hikayesi yeniden sahnede.',
                'type' => 'theatre',
                'event_date' => Carbon::now()->addDays(7),
                'location' => 'Ankara Devlet Tiyatrosu',
                'price' => 150.00,
                'available_tickets' => 300,
                'weather_dependent' => false,
                'categories' => ['theatre', 'drama', 'classic'],
                'image_url' => 'https://example.com/images/romeo-juliet.jpg'
            ]
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
