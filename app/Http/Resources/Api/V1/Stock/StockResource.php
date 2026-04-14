<?php

namespace App\Http\Resources\Api\V1\Stock;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id'        => $this->product_id,
            'product_name'      => $this->product_name,
            'sku'               => $this->sku,
            'warehouse_id'      => $this->warehouse_id,
            'warehouse_name'    => $this->warehouse_name,
            'quantity'          => (int) $this->quantity,
            'reserved_quantity' => (int) $this->reserved_quantity,
            'available_quantity'=> (int) $this->available_quantity,
        ];
    }
}