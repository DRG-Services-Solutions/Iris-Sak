<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', 
            'tenant_id' => [
                Rule::requiredIf(fn () => auth()->user()->hasRole('Super Admin')),
                'nullable',
                'exists:tenants,id',
                function ($attribute, $value, $fail) {
                    if (auth()->user()->hasRole('Super Admin')) {
                        $userCount = \App\Models\User::where('tenant_id', $value)->count();
                        if ($userCount > 0) {
                            $fail('Esta empresa ya tiene un administrador. No puedes crearle más usuarios.');
                        }
                    }
                },
            ],
            'role' => [
                Rule::requiredIf(fn () => !auth()->user()->hasRole('Super Admin')),
                'nullable',
                'exists:roles,name'
            ],
        ];
    
    }
}
