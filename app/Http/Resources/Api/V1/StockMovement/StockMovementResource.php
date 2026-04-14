<?php

namespace App\Http\Resources\Api\V1\StockMovement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
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
            'product_id' => $this->product_id,
            'warehouse_id' => $this->warehouse_id,
            'movement_type' => $this->movement_type,
            'movement_type_name' => $this->movement_type_name,
            'quantity' => (int) $this->quantity,
            'reference_id' => $this->reference_id,
            'reference_type' => $this->reference_type,
            'note' => $this->note,
            'moved_at' => $this->moved_at,
            'moved_at_formatted' => $this->moved_at?->format('Y-m-d H:i:s'),
        ];
    }
}