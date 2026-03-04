<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:tenants,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la empresa es requerido.',
            'name.string' => 'El nombre de la empresa debe ser una cadena de texto.',
            'name.max' => 'El nombre de la empresa no puede tener más de 255 caracteres.',
            'name.unique' => 'El nombre de la empresa ya ha sido tomado.',
        ];
    }
}
