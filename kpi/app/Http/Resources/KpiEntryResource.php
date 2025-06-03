<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KpiEntryResource extends JsonResource
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
            'code' => $this->code,
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'product' => ProductResource::make($this->whenLoaded('product')),
            'supplier' => SupplierResource::make($this->whenLoaded('supplier')),
            'month' => $this->month,
            'uom' => $this->uom,
            'quantity' => $this->quantity,
            'asp' => $this->asp,
            'total_value' => $this->total_value,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
