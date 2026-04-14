<?php

namespace App\Http\Resources\Api\V1\StockMovement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_in' => (int) ($this->total_in ?? 0),
            'total_out' => (int) ($this->total_out ?? 0),
            'net_movement' => (int) ($this->net_movement ?? 0),
        ];
    }
}