<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_admin',
        'approved',
        'password_changed'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'approved' => 'boolean',
            'password_changed' => 'boolean',
        ];
    }

    /**
     * Kullanıcının oluşturduğu etkinlikler ile ilişki
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Kullanıcının katıldığı etkinlikler ile ilişki
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participatingEvents()
    {
        return $this->belongsToMany(Event::class, 'tickets', 'user_id', 'event_id')
            ->withPivot('status', 'purchase_date')
            ->withTimestamps();
    }

    /**
     * Kullanıcı admin mi?
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Kullanıcı normal kullanıcı mı?
     */
    public function isUser()
    {
        return $this->role === 'user';
    }

    // Hesap onay durumunu kontrol et
    public function getIsApprovedAttribute()
    {
        return $this->approved;
    }

    // Hesap onay durumunu güncelle
    public function setIsApprovedAttribute($value)
    {
        $this->attributes['approved'] = $value;
    }
}
