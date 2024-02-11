<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlatilloRequest extends FormRequest
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
            'id' => 'sometimes|exists:platillos,id',
            'name' => 'required|string',
            'description' => 'required|string',
            'hr' => 'nullable|numeric|min:0|max:8',
            'min' => 'required|numeric|min:0|max:59',
            'cost' => 'required|numeric',
            'price' => 'required|numeric',
            'sucursal' => 'required|array',
            'sucursal.*' => 'exists:sucursals,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'description.required' => 'La descripción es obligatoria',
            'hr.required' => 'Las horas son obligatorias',
            'min.required' => 'Los minutos son obligatorios',
            'cost.required' => 'El costo es obligatorio',
            'price.required' => 'El precio es obligatorio',
            'sucursal.required' => 'La sucursal es obligatoria',
        ];
    }
}
