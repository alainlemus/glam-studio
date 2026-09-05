<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class Audit
{
    /**
     * Record a sensitive admin action for later review. Kept deliberately
     * narrow — only actions that are hard to undo or affect permissions/money
     * are logged (deletions, role/price changes), not every click.
     *
     * @param  array<string, mixed>  $changes  e.g. ['price' => ['old' => 100, 'new' => 150]]
     */
    public static function record(string $action, ?Model $subject, string $description, array $changes = []): AuditLog
    {
        return AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'auditable_type' => $subject ? $subject::class : null,
            'auditable_id' => $subject?->getKey(),
            'description' => $description,
            'changes' => $changes ?: null,
            'ip_address' => request()?->ip(),
        ]);
    }
}
