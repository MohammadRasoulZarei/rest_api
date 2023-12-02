<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       // return parent::toArray($request);
       return[
        'id'=>$this->id,
        'name'=>$this->name,
        'display_name'=>$this->display_name,
        'products'=>ProductResource::collection($this->whenLoaded('products')->load('images')),
       // 'productImages'=>productImageResource::collection($this->products->load('images')),
        'created_at'=>$this->created_at,
       ];
    }
}