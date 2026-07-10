<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'client_id', 'branch_id', 'stylist_id',
        'date', 'start_time', 'end_time', 'status', 'source',
        'total', 'deposit', 'notes', 'cancellation_reason', 'reminder_sent_at',
    ];

    protected $casts = [
        'date' => 'date',
        'reminder_sent_at' => 'datetime',
        'total' => 'decimal:2',
        'deposit' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Appointment $appointment) {
            if (empty($appointment->code)) {
                $appointment->code = 'CITA-' . strtoupper(Str::random(8));
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function stylist(): BelongsTo
    {
        return $this->belongsTo(Stylist::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(AppointmentService::class);
    }

    public function sale(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Sale::class);
    }

    public function loyaltyStamps(): HasMany
    {
        return $this->hasMany(LoyaltyStamp::class);
    }

    public function getDurationAttribute(): int
    {
        $start = strtotime($this->start_time);
        $end = strtotime($this->end_time);
        return (int) (($end - $start) / 60);
    }

    public function isUpcoming(): bool
    {
        return $this->date->isFuture() || ($this->date->isToday() && strtotime($this->start_time) > time());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', today())
            ->whereNotIn('status', ['cancelled', 'completed']);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForStylist($query, $stylistId)
    {
        return $query->where('stylist_id', $stylistId);
    }

    public static function statuses(): array
    {
        return ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'];
    }

    public static function statusColors(): array
    {
        return [
            'pending' => '#f59e0b',
            'confirmed' => '#3b82f6',
            'in_progress' => '#8b5cf6',
            'completed' => '#10b981',
            'cancelled' => '#ef4444',
            'no_show' => '#6b7280',
        ];
    }

    public function getStatusColorAttribute(): string
    {
        return self::statusColors()[$this->status] ?? '#6b7280';
    }
}