<?php

namespace App\Http\Resources;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\productImageResource;
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
            'id'=>$this->id,
            'name'=>$this->name,
            'category'=>new CategoryResource($this->whenLoaded('category')),
            'brand'=>new BrandResource($this->whenLoaded('brand')),
            'primary_image'=>url(env('PRODUCT_IAMGE_PATH').$this->primary_image),
            'price'=>$this->price,
            'quantity'=>$this->quantity,
            'description'=>$this->description,
            'images'=>productImageResource::collection($this->whenLoaded('images'))
        ];
    }
}
