<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalculateItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'cup'          => $this['cup'],
            'water'        => $this['water'],
            'extras'       => $this['extras'],
            'extras_price' => (float) $this['extras_price'],
            'unit_price'   => (float) $this['unit_price'],
            'quantity'     => $this['quantity'],
            'line_total'   => (float) $this['line_total'],
        ];
    }
}
