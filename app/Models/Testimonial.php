<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name', 'quote', 'rating', 'photo_path', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? Storage::disk('public')->url($this->photo_path) : null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
