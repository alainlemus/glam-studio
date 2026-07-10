<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'type', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (ExpenseCategory $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function expenses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFixed($query)
    {
        return $query->where('type', 'fixed');
    }

    public function scopeVariable($query)
    {
        return $query->where('type', 'variable');
    }

    public static function defaults(): array
    {
        return [
            ['name' => 'Renta', 'type' => 'fixed'],
            ['name' => 'Sueldos', 'type' => 'fixed'],
            ['name' => 'Servicios (Luz, Agua, Internet)', 'type' => 'fixed'],
            ['name' => 'Producto', 'type' => 'variable'],
            ['name' => 'Insumos', 'type' => 'variable'],
            ['name' => 'Marketing', 'type' => 'variable'],
            ['name' => 'Mantenimiento', 'type' => 'variable'],
            ['name' => 'Otros', 'type' => 'variable'],
        ];
    }
}