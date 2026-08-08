<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
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
            'description'         => $this->description,
            'original_name'       => $this->original_name,
            'mime_type'           => $this->mime_type,
            'file_size'           => (int) $this->file_size,
            'formatted_file_size' => $this->formatted_file_size,
            'file_url'            => $this->file_url,
            'download_url'        => $this->download_url,
            'downloads_count'     => (int) $this->downloads_count,
            'status'              => $this->status,
            'published_at'        => $this->published_at?->toISOString(),
            'category'            => $this->whenLoaded('category', fn() => [
                'id'          => $this->category->id,
                'name'        => $this->category->name,
                'slug'        => $this->category->slug,
                'description' => $this->category->description,
            ]),
            'created_at'          => $this->created_at?->toISOString(),
            'updated_at'          => $this->updated_at?->toISOString(),
        ];
    }
}
