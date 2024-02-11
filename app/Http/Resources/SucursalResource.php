<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SucursalResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'tables' => $this->tables,
            'tablesOp' => $this->tablesOp(),
            'address' => $this->address ? new AddressResource($this->address) : $this->address,
            'platillos' => $this->platillos(),
        ];
    }
}
