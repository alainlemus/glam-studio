<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;

class NotificationRecipients
{
    /**
     * Staff who should be notified about an event tied to a branch: every
     * admin (sees everything) plus the managers/receptionists assigned to
     * that specific branch. Pass null for branch-agnostic events (all admins only).
     *
     * @return Collection<int, User>
     */
    public static function forBranch(?int $branchId): Collection
    {
        return User::where('is_active', true)
            ->where(function ($query) use ($branchId) {
                $query->where('role', User::ROLE_ADMIN);

                if ($branchId) {
                    $query->orWhere(function ($query2) use ($branchId) {
                        $query2->whereIn('role', [User::ROLE_MANAGER, User::ROLE_RECEPTIONIST])
                            ->where('branch_id', $branchId);
                    });
                }
            })
            ->get();
    }
}
