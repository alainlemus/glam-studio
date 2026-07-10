<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'type', 'start_date', 'end_date',
        'branch_id', 'service_id', 'discount_percentage',
        'message_template', 'image', 'status',
        'target_audience', 'messages_sent',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'discount_percentage' => 'decimal:2',
        'target_audience' => 'integer',
        'messages_sent' => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function isActive(): bool
    {
        $now = now();
        if ($this->end_date && $this->end_date->lt($now)) {
            return false;
        }
        return $this->status === 'active' && $this->start_date->lte($now);
    }
}