<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyStamp extends Model
{
    use HasFactory;

    protected $fillable = [
        'loyalty_card_id', 'appointment_id', 'type', 'quantity', 'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function loyaltyCard(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCard::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}