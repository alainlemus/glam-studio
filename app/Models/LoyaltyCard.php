<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LoyaltyCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'code', 'stamps_required',
        'stamps_current', 'total_rewards_claimed', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'stamps_required' => 'integer',
        'stamps_current' => 'integer',
        'total_rewards_claimed' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (LoyaltyCard $card) {
            if (empty($card->code)) {
                $card->code = 'LOY-' . strtoupper(Str::random(8));
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function stamps(): HasMany
    {
        return $this->hasMany(LoyaltyStamp::class);
    }

    public function addStamp(int $qty = 1): void
    {
        $this->increment('stamps_current', $qty);
    }

    public function redeemReward(): bool
    {
        if ($this->stamps_current < $this->stamps_required) {
            return false;
        }
        $this->decrement('stamps_current', $this->stamps_required);
        $this->increment('total_rewards_claimed');
        return true;
    }

    public function progressPercent(): float
    {
        if ($this->stamps_required == 0) {
            return 0;
        }
        return min(100, ($this->stamps_current / $this->stamps_required) * 100);
    }
}