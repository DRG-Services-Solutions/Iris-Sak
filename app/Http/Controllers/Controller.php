<?php

namespace App\Http\Controllers;

// Estas son las importaciones y la herencia correctas
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests; // Puede ser también DispatchesJobs en algunas versiones/configuraciones
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController // Asegúrate que extienda BaseController
{
    // Asegúrate que use estos Traits
    use AuthorizesRequests, ValidatesRequests;
}