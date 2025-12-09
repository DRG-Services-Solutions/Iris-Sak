<?php

namespace App\Policies;

use App\Models\InventoryCount;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InventoryCountPolicy
{
    /**
     * Determine whether the user can view any models.
     * Todos los usuarios autenticados pueden ver el listado
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * El usuario puede ver sus propios conteos o si es admin
     */
    public function view(User $user, InventoryCount $inventoryCount): bool
    {
        return $user->id === $inventoryCount->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     * Todos los usuarios autenticados pueden crear conteos de inventario
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * Solo se puede actualizar si está en proceso y es el dueño o admin
     */
    public function update(User $user, InventoryCount $inventoryCount): bool
    {
        $isOwnerOrAdmin = $user->id === $inventoryCount->user_id || $user->isAdmin();
        $isInProgress = $inventoryCount->status === 'en_proceso';

        return $isOwnerOrAdmin && $isInProgress;
    }

    /**
     * Determine whether the user can delete the model.
     * Solo admins pueden eliminar conteos
     */
    public function delete(User $user, InventoryCount $inventoryCount): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, InventoryCount $inventoryCount): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, InventoryCount $inventoryCount): bool
    {
        return $user->isAdmin();
    }
}
