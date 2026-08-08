<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationStepResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'title'                  => $this->title,
            'slug'                   => $this->slug,
            'description'            => $this->description,
            'requirements'           => $this->requirements,
            'icon'                   => $this->icon,
            'illustration_image_url' => $this->illustration_image_url,
            'sort_order'             => (int) $this->sort_order,
            'status'                 => $this->status,
            'created_at'             => $this->created_at?->toISOString(),
            'updated_at'             => $this->updated_at?->toISOString(),
        ];
    }
}
