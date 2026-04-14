<?php

namespace App\Http\Resources\Api\V1\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventorySummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'total_quantity' => (int) $this->total_quantity,
            'total_reserved' => (int) $this->total_reserved,
            'available_quantity' => (int) $this->available_quantity,
            'top_warehouse_id' => $this->top_warehouse_id ?? "",
            'top_warehouse_name' => $this->top_warehouse_name ?? "",
        ];
    }
}