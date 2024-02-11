<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => explode(' ', $this->name)[0],
            'first_name' => explode(' ', $this->name)[1] ?? '',
            'email' => $this->email,
            'telephone' => $this->telephone,
            'nss' => $this->nss,
            'rfc' => $this->rfc,
            'salary' => $this->salary,
            'address' => $this->address ? new AddressResource($this->address) : $this->address,
        ];
    }
}
