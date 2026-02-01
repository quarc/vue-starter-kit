<?php

namespace App\Navigation\Contracts;

trait HasPermissions
{
    /**
     * Check if the user has the permission
     */
    protected function checkPermission(?string $permission): bool
    {
        if (empty($permission)) {
            return true;
        }

        $user = auth()->user();

        if (! $user) {
            return false;
        }

        try {
            if (! \Illuminate\Support\Facades\Gate::has($permission)) {
                return true;
            }

            return \Illuminate\Support\Facades\Gate::forUser($user)->check($permission);
        } catch (\Exception $e) {
            return true;
        }
    }
}
