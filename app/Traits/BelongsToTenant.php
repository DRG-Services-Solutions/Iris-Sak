<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        // 1. El Global Scope: Intercepta y filtra las consultas de lectura automáticamente
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();

                // EXCEPCIÓN: Si es Super Admin, nos salimos y NO aplicamos el filtro. (Ve todo)
                if ($user->hasRole('Super Admin')) {
                    return;
                }

                // REGLA NORMAL: Si es un usuario de un tenant, filtramos la info.
                if ($user->tenant_id) {
                    $builder->where('tenant_id', $user->tenant_id);
                }
            }
        });

        static::creating(function ($model) {
            if (auth()->check()) {
                $user = auth()->user();

                // Si NO es Super Admin, y tiene un tenant_id, se lo asignamos al nuevo registro
                if (! $user->hasRole('Super Admin') && $user->tenant_id && empty($model->tenant_id)) {
                    $model->tenant_id = $user->tenant_id;
                }
            }
        });
    }
}