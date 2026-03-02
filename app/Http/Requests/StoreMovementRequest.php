<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovementRequest extends FormRequest
{

    /**
     * Permitir que la Policy maneje la autorización.
     */
    public function authorize(): bool
    {
        // Retornamos true porque la seguridad la manejará la Policy
        return true;
    }

    /**
     * Reglas granulares de negocio.
     */
    public function rules(): array
    {
        return [
            'product_id'          => ['required', 'exists:products,id'],
            'product_instance_id' => ['nullable', 'exists:product_instances,id'],
            'type'                => ['required', 'in:in,out,adjustment'],
            'quantity'            => ['required', 'integer', 'min:1'],
            'notes'               => ['nullable', 'string', 'max:500'],
            
        ];
    }
}