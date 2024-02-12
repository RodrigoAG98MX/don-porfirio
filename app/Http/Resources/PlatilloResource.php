<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlatilloResource extends JsonResource
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
            'description' => $this->description,
            'preparation_time' => $this->setTime(),
            'cost' => $this->cost,
            'price' => $this->price,
            'sucursal' => json_decode($this->sucursal),
        ];
    }

    protected function setTime()
    {
        $time = [];
        $time['hours'] = (int)explode(':', $this->preparation_time)[0];
        $time['minutes'] = (int)explode(':', $this->preparation_time)[1];
        $time['seconds'] = (int)explode(':', $this->preparation_time)[2];
        return $time;
    }
}
