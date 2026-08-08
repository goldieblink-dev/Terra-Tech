<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'title'               => $this->title,
            'slug'                => $this->slug,
            'content'             => $this->content,
            'status'              => $this->status,
            'views_count'         => (int) $this->views_count,
            'published_at'        => $this->published_at?->toISOString(),
            'featured_image_url'  => $this->featured_image_url,
            'category'            => $this->whenLoaded('category', fn() => [
                'id'          => $this->category->id,
                'name'        => $this->category->name,
                'slug'        => $this->category->slug,
                'description' => $this->category->description,
            ]),
            'creator'             => $this->whenLoaded('creator', fn() => [
                'id'   => $this->creator->id,
                'name' => $this->creator->name,
            ]),
            'created_at'          => $this->created_at?->toISOString(),
            'updated_at'          => $this->updated_at?->toISOString(),
        ];
    }
}
