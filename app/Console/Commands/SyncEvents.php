<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TicketmasterService;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SyncEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Süresi geçmiş etkinlikleri siler ve API\'den yeni etkinlikleri çeker';

    protected $ticketmasterService;

    public function __construct(TicketmasterService $ticketmasterService)
    {
        parent::__construct();
        $this->ticketmasterService = $ticketmasterService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Etkinlik senkronizasyonu başlatılıyor...');

        try {
            // Süresi geçmiş etkinlikleri sil
            $deletedCount = Event::where('event_date', '<', Carbon::now())
                                ->delete();
            
            $this->info("{$deletedCount} adet süresi geçmiş etkinlik silindi.");

            // API'den yeni etkinlikleri çek
            // Örnek olarak İstanbul için etkinlikleri çekiyoruz, ihtiyaca göre şehir listesi genişletilebilir
            $cities = ['Istanbul', 'Ankara', 'Izmir'];
            $totalNewEvents = 0;

            foreach ($cities as $city) {
                $events = $this->ticketmasterService->getEventsByCity($city);

                if ($events) {
                    foreach ($events as $eventData) {
                        // Event modelinize göre veri yapısını ayarlayın
                        $eventDate = Carbon::parse($eventData['dates']['start']['dateTime'] ?? null);
                        
                        // Sadece gelecekteki etkinlikleri ekle
                        if ($eventDate->isFuture()) {
                            Event::updateOrCreate(
                                ['api_id' => $eventData['id']],
                                [
                                    'title' => $eventData['name'],
                                    'event_date' => $eventDate,
                                    'location' => $eventData['_embedded']['venues'][0]['name'] ?? 'Belirtilmemiş',
                                    'type' => $eventData['classifications'][0]['segment']['name'] ?? 'Genel',
                                    'is_published' => true,
                                    // Diğer gerekli alanları buraya ekleyin
                                ]
                            );
                            $totalNewEvents++;
                        }
                    }
                }
            }

            $this->info("{$totalNewEvents} adet yeni etkinlik eklendi veya güncellendi.");
            Log::info("Etkinlik senkronizasyonu tamamlandı. Silinen: {$deletedCount}, Eklenen/Güncellenen: {$totalNewEvents}");
        } catch (\Exception $e) {
            $this->error('Etkinlik senkronizasyonu sırasında bir hata oluştu: ' . $e->getMessage());
            Log::error('Etkinlik senkronizasyonu hatası: ' . $e->getMessage());
        }
    }
}
