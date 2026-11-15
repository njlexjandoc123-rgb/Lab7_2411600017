<?php

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;

class PurchaseOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $purchaseOrder->status === 'draft'
            && ($user->hasRole('admin') || $purchaseOrder->created_by === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->hasRole('admin') && $purchaseOrder->status === 'draft';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return false;
    }

    public function approve(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->hasRole('admin', 'manager') && $purchaseOrder->canBeApproved();
    }

    public function receive(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->hasRole('admin', 'warehouse') && $purchaseOrder->canBeReceived();
    }

    public function reject(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->hasRole('admin', 'manager') && $purchaseOrder->canBeApproved();
    }

    public function send(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->hasRole('admin', 'manager') && $purchaseOrder->status === 'approved';
    }
}
