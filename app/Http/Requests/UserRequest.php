<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'id' => 'sometimes|exists:users,id',
            'name' => 'required|string',
            'first_name' => 'required|string',
            'email' => "required|string|email|unique:users,email,$request->id",
            'telephone' => 'required|string|phone:mx',
            'nss' => "required|string|unique:users,nss,$request->id|digits:11",
            'rfc' => "required|string|unique:users,rfc,$request->id|min:12|max:13",
            'salary' => 'required|numeric',
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
            'telephone.phone' => 'El télefono es invalido',
            'nss.required' => 'El nss es obligatorio',
            'nss.digits' => 'El nss debe ser de 11 digitos',
            'rfc.required' => 'El rfc es obligatorio',
            'rfc.digits' => 'El rfc debe ser de 13 digitos',
            'salary.required' => 'El salario es obligatorio',
            'password.required' => 'La contraseña es obligatorio',
            'street.required' => 'La calle es obligatoria',
            'number.required' => 'El número es obligatorio',
            'suburb.required' => 'La colonia es obligatoria',
            'cp.required' => 'El cp es obligatorio',
            'cp.digits' => 'El cp debe ser de 5 digitos',
            'references.required' => 'La referencia es obligatoria',
            'state.required' => 'El estado es obligatorio',
            'country.required' => 'El país es obligatorio',
        ];
    }
}
