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
            'user_id' => 'required|exists:users,id',
            'propina' => 'required|numeric',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'sucursal_id' => 'required|exists:sucursals,id',
        ];
    }
}
