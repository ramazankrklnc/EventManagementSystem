<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Event Model
 * 
 * Bu model etkinliklerin veritabanı işlemlerini yönetir.
 * SoftDeletes trait'i ile silinen etkinliklerin geri getirilebilmesini sağlar.
 */
class Event extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Toplu atama yapılabilecek alanlar
     * 
     * @var array
     */
    protected $fillable = [
        'title',           // Etkinlik başlığı
        'description',     // Etkinlik açıklaması
        'type',            // Etkinlik türü
        'event_date',      // Etkinlik tarihi ve saati
        'location',        // Etkinlik konumu
        'capacity',        // Yeni eklenen kapasite alanı
        'image_url',       // Etkinlik görseli URL'i
        'weather_dependent', // Hava koşullarına bağlı mı
        'categories',       // Etkinlik kategorileri
        'is_published'     // Etkinlik yayın durumu
    ];

    /**
     * Veri tiplerini otomatik dönüştür
     * 
     * @var array
     */
    protected $casts = [
        'event_date' => 'datetime',        // Tarih ve saat formatına dönüştür
        'weather_dependent' => 'boolean',    // Boolean
        'categories' => 'array',            // Dizi
        'is_published' => 'boolean',       // Boolean (yayın durumu için)
        'capacity' => 'integer'            // Tam sayı
    ];

    /**
     * Etkinliği oluşturan kullanıcı ile ilişki
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Etkinliğin biletleri ile ilişki
     * Bir etkinliğin birden çok bileti olabilir
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Etkinliğe katılan kullanıcılar ile ilişki
     * Biletler üzerinden many-to-many ilişki
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'tickets', 'event_id', 'user_id')
            ->withPivot('status', 'purchase_date')
            ->withTimestamps();
    }

    /**
     * Etkinliğin aktif olup olmadığını kontrol eder
     * 
     * @return bool
     */
    public function isActive()
    {
        // Şimdilik sadece yayınlanma durumuna ve tarihin geçmemiş olmasına bakalım.
        // Eğer 'status' alanı farklı bir amaçla kullanılacaksa bu metodun mantığı güncellenmeli.
        return $this->is_published && $this->event_date->isFuture();
    }

    /**
     * Etkinliğin iptal edilip edilmediğini kontrol eder
     * 
     * @return bool
     */
    // public function isCancelled()
    // {
    //     return $this->status === 'cancelled'; // Şimdilik yorum satırı, 'status' alanı yok
    // }

    /**
     * Etkinliğin tamamlanıp tamamlanmadığını kontrol eder
     * 
     * @return bool
     */
    // public function isCompleted()
    // {
    //     return $this->status === 'completed'; // Şimdilik yorum satırı, 'status' alanı yok
    // }

    /**
     * Etkinlik için bilet satın alınıp alınamayacağını kontrol eder
     * Etkinlik aktif ve bilet mevcut olmalıdır
     * 
     * @return bool
     */
    public function canPurchaseTicket()
    {
        return $this->isActive() && $this->available_tickets > 0;
    }

    // Etkinlikleri tarihe göre sıralama için scope
    public function scopeOrderByDate($query)
    {
        return $query->orderBy('event_date', 'asc');
    }

    /**
     * Sadece yayınlanmış etkinlikleri getiren scope
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Kullanıcının ilgi alanlarına göre etkinlik önerileri
    public function scopeMatchingInterests($query, array $userInterests)
    {
        return $query->whereJsonContains('categories', $userInterests);
    }

    /**
     * Etkinliğin bilet tipleri ile ilişki
     * Bir etkinliğin birden çok bilet tipi olabilir
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class);
    }
}
