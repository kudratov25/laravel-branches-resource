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

        return [
            'id' => $this->id,
            'name' => $this->name,
            'domain' => $this->domain,
            'status' => $this->status,
            'image_path' => $this->image_path ? url('storage/' . $this->image_path) : null,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
