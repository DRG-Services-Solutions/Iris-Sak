<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Auth\Access\Response;

class WorkOrderPolicy
{

    /**
     * Determine whether the user can audit the work order with RFID.
     */
    public function auditRfid(User $user, WorkOrder $workOrder): bool
    {
        // Solo admins pueden auditar, y solo si la orden está 'Enviado'
        return $user->isAdmin() && $workOrder->status === 'Enviado';
    }
    /**
     * Helper function to check if the order is considered shipped/completed.
     */
    private function isShipped(WorkOrder $workOrder): bool
    {
        // Consideramos enviada si el estado es 'Enviado' O si tiene fecha de completado
        return $workOrder->status === 'Enviado' || $workOrder->completed_at !== null;
    }

    /**
     * Determine whether the user can view any models.
     * Permitir a todos los usuarios logueados ver la lista (ajustar si es necesario).
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin(); 
    }

    /**
     * Determine whether the user can view the model.
     * Permitir a todos ver detalles (ajustar si es necesario).
     */
    public function view(User $user, WorkOrder $workOrder): bool
    {
        return $user->id === $workOrder->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     * Permitir a todos crear órdenes por ahora.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * NO se puede actualizar si ya está enviada.
     */
    public function update(User $user, WorkOrder $workOrder): bool
    {
        // Podrías añadir && $user->isAdmin() si solo admins pueden editar
        return !$this->isShipped($workOrder);
    }

    /**
     * Determine whether the user can delete the model.
     * NO se puede borrar si ya está enviada.
     */
    public function delete(User $user, WorkOrder $workOrder): bool
    {
        // Podrías añadir && $user->isAdmin() si solo admins pueden borrar
        return !$this->isShipped($workOrder);
    }

    /**
     * Determine whether the user can restore the model.
     * (Solo relevante si usas Soft Deletes en WorkOrder)
     */
    // public function restore(User $user, WorkOrder $workOrder): bool
    // {
    //     return $user->isAdmin(); // Ejemplo
    // }

    /**
     * Determine whether the user can permanently delete the model.
     * (Solo relevante si usas Soft Deletes en WorkOrder)
     */
    // public function forceDelete(User $user, WorkOrder $workOrder): bool
    // {
    //     return $user->isAdmin(); // Ejemplo
    // }


    // --- MÉTODOS PERSONALIZADOS PARA NUESTRAS ACCIONES ---

    /**
     * Determina si se pueden añadir instancias (escanear) a la orden.
     * NO si ya está enviada.
     */
    public function addInstance(User $user, WorkOrder $workOrder): bool
    {
        return !$this->isShipped($workOrder);
    }

    /**
     * Determina si la orden se puede liberar/marcar como enviada.
     * NO si ya está enviada Y DEBE estar en Estación 02.
     */
    public function release(User $user, WorkOrder $workOrder): bool
    {
        // Solo se puede liberar si NO está ya enviada Y está en la estación correcta
        return !$this->isShipped($workOrder) && $workOrder->station === '02';
    }
}