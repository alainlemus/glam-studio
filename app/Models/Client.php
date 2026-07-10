<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'phone', 'email', 'birthday',
        'notes', 'no_show_count', 'is_blocked', 'is_active',
    ];

    protected $casts = [
        'birthday' => 'date',
        'no_show_count' => 'integer',
        'is_blocked' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function loyaltyCard(): HasOne
    {
        return $this->hasOne(LoyaltyCard::class);
    }

    public function whatsappUrl(): ?string
    {
        if (!$this->phone) {
            return null;
        }
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        return "https://wa.me/{$phone}";
    }

    public function canBook(): bool
    {
        return $this->is_active && !$this->is_blocked && $this->no_show_count < 3;
    }

    public function registerNoShow(): void
    {
        $this->increment('no_show_count');
        if ($this->no_show_count >= 3) {
            $this->update(['is_blocked' => true]);
        }
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}