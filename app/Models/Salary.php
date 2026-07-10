<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'stylist_id', 'branch_id', 'period_start', 'period_end',
        'base_salary', 'commissions_total', 'bonuses', 'deductions',
        'total', 'status', 'paid_at', 'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'base_salary' => 'decimal:2',
        'commissions_total' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'deductions' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function stylist(): BelongsTo
    {
        return $this->belongsTo(Stylist::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function calculateTotal(): void
    {
        $this->total = $this->base_salary + $this->commissions_total + $this->bonuses - $this->deductions;
        $this->save();
    }

    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid', 'paid_at' => now()]);
    }
}