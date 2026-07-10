<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stylist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'branch_id', 'specialty', 'bio', 'photo',
        'base_salary', 'service_commission', 'product_commission', 'is_active',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'service_commission' => 'decimal:2',
        'product_commission' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(StylistSchedule::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->user?->name ?? 'Sin nombre';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function totalSalesInPeriod($start, $end): float
    {
        return (float) $this->sales()
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'paid')
            ->sum('total');
    }

    public function pendingCommissions(): float
    {
        return (float) $this->commissions()
            ->where('status', 'pending')
            ->sum('amount');
    }
}