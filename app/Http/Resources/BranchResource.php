<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->load('images');
        return [
            'id' => $this->id,
            'name' => $this->name,
            'brand_name' => $this->brand->name,
            'district_name' => $this->district->name_uz,
            'images' => $this->images->map(function ($image) {
                return [
                    'url' => $image->url,
                ];
            }),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
