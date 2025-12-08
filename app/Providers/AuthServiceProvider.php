<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use App\Models\WorkOrder;
use App\Policies\WorkOrderPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }
     /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy', // Laravel puede descubrir esto automáticamente a veces
        WorkOrder::class => WorkOrderPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // --- Definición de Gates ---

        // Gate para permitir la gestión completa del catálogo de productos
        // Solo los usuarios con rol 'admin' podrán pasar este Gate.
        Gate::define('manage-products', function (User $user) {
            return $user->isAdmin(); // Usamos el helper que creamos en el modelo User
        });

        // Aquí podríamos definir otros Gates en el futuro, por ejemplo:
        // Gate::define('use-tracking-system', function (User $user) {
        //     // Quizás todos los usuarios (admin y user) pueden usar el sistema de rastreo
        //     return $user->role === User::ROLE_USER || $user->isAdmin();
        // });
        //
        // Gate::define('manage-users', function (User $user) {
        //     // Solo admins pueden gestionar usuarios
        //     return $user->isAdmin();
        // });

        // -------------------------
    }
}
