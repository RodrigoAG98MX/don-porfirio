<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'street' => $this->street,
            'number' => $this->number,
            'suburb' => $this->suburb,
            'cp' => $this->cp,
            'references' => $this->references,
            'state' => $this->state,
            'country' => $this->country,
        ];
    }
}
