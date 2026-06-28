<?php

namespace App\Policies;

use App\Models\FlowerChecklist;
use App\Models\User;

class ChecklistPolicy
{
    /**
     * Admins can view any checklist; staff can view their own.
     */
    public function view(User $user, FlowerChecklist $checklist): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->id === $checklist->user_id;
    }

    /**
     * Any authenticated user can create.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Super Admins and Admins can edit any; staff can only edit their own.
     */
    public function update(User $user, FlowerChecklist $checklist): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->id === $checklist->user_id;
    }

    /**
     * Only super admins and admins can delete.
     * Admins cannot delete inspections created by super admins.
     */
    public function delete(User $user, FlowerChecklist $checklist): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isAdmin()) {
            // Check if the checklist belongs to a super admin
            return !$checklist->user?->isSuperAdmin();
        }

        return false;
    }

    /**
     * Super Admins and Admins can export.
     */
    public function export(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }
}
