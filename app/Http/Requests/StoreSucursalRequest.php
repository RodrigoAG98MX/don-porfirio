<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSucursalRequest extends FormRequest
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
        $request = $this;
        return [
            'id' => 'sometimes|exists:sucursals,id',
            'name' => 'required|string',
            'email' => "required|string|email|unique:users,email,$request->id",
            'telephone' => 'required|string|phone:mx',
            'tables' => 'required|integer|min:0',
            'street' => 'required|string',
            'number' => 'required|string',
            'suburb' => 'required|string',
            'cp' => 'required|string|digits:5',
            'references' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo es obligatorio',
            'email.email' => 'El correo es invalido',
            'telephone.required' => 'El teléfono es obligatorio',
            'street.required' => 'La calle es obligatoria',
            'number.required' => 'El número es obligatorio',
            'suburb.required' => 'La colonia es obligatoria',
            'cp.required' => 'El cp es obligatorio',
            'cp.digits' => 'El cp debe ser de 5 digitos',
            'references.required' => 'La referencia es obligatoria',
            'state.required' => 'El estado es obligatorio',
            'country.required' => 'El país es obligatorio',
            'telephone.phone' => 'El télefono es invalido'
        ];
    }
}
