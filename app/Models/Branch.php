<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id', 'name', 'slug', 'address', 'phone', 'whatsapp',
        'email', 'manager_name', 'opening_time', 'closing_time',
        'opening_days', 'latitude', 'longitude', 'image', 'description', 'is_active',
    ];

    protected $casts = [
        'opening_days' => 'array',
        'is_active' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    protected static function booted(): void
    {
        static::creating(function (Branch $branch) {
            if (empty($branch->slug)) {
                $branch->slug = Str::slug($branch->name);
            }
        });
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function stylists(): HasMany
    {
        return $this->hasMany(Stylist::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function productStocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function whatsappUrl(): ?string
    {
        if (!$this->whatsapp) {
            return null;
        }
        $phone = preg_replace('/[^0-9]/', '', $this->whatsapp);
        return "https://wa.me/{$phone}";
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}