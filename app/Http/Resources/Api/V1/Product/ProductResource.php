<?php

namespace App\Http\Resources\Api\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'sku' => $this->sku,
            'price' => $this->base_price,
            'category' => $this->category?->name,

            'available_stock' => $this->stocks->sum(function ($s) {
                return $s->quantity - $s->reserved_quantity;
            }),
        ];
    }
}
