<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VentaRequest extends FormRequest
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
            'number_table' => 'required|numeric',
            'mesero' => 'required|exists:users,id',
            'platillos' => 'required|array',
            'platillos.*.id' => 'exists:platillos,id',
            'platillos.*.total' => 'numeric',
            'propina' => 'required|numeric',
            'date' => 'required|date',
            'time' => 'required',
            'time.hours' => 'numeric',
            'time.minutes' => 'numeric',
            'sucursal' => 'required|exists:sucursals,id',
            'amount' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'time.required' => 'La hora es obligatoria'
        ];
    }
}
