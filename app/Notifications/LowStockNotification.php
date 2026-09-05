<?php

namespace App\Notifications;

use App\Models\ProductStock;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(public ProductStock $stock) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'icon' => 'package-x',
            'color' => 'amber',
            'title' => 'Stock bajo',
            'message' => "{$this->stock->product?->name} en {$this->stock->branch?->name} tiene {$this->stock->stock} unidades (mínimo {$this->stock->min_stock}).",
            'url' => '/admin/inventory',
        ];
    }
}
