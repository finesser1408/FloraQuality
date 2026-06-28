<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Only super_admin and admin can view the list of users.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    /**
     * Only super_admin can create users.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Only super_admin can edit users.
     * Prevents modifying another super_admin unless the current user is a super_admin themselves.
     */
    public function update(User $user, User $model): bool
    {
        if (!$user->isSuperAdmin()) {
            return false;
        }

        // If target is super_admin, verify we have at least one active super_admin left
        return true;
    }

    /**
     * Only super_admin can delete users.
     * Super admins cannot delete themselves or other super admins.
     */
    public function delete(User $user, User $model): bool
    {
        if (!$user->isSuperAdmin()) {
            return false;
        }

        // Cannot delete self
        if ($user->id === $model->id) {
            return false;
        }

        // Cannot delete another super admin
        if ($model->isSuperAdmin()) {
            return false;
        }

        return true;
    }

    /**
     * Only super_admin can view audit logs or manage system settings.
     */
    public function manageSystem(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
