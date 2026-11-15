<?php

namespace App\Policies;

use App\Models\User;

class SupplierPolicy
{
    public function restore(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
