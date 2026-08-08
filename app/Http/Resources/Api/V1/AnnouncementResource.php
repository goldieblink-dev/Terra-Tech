<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'slug'            => $this->slug,
            'content'         => $this->content,
            'attachment_name' => $this->attachment_name,
            'attachment_url'  => $this->attachment_url,
            'priority'        => $this->priority,
            'status'          => $this->status,
            'downloads_count' => (int) $this->downloads_count,
            'published_at'    => $this->published_at?->toISOString(),
            'creator'         => $this->whenLoaded('creator', fn() => [
                'id'   => $this->creator->id,
                'name' => $this->creator->name,
            ]),
            'created_at'      => $this->created_at?->toISOString(),
            'updated_at'      => $this->updated_at?->toISOString(),
        ];
    }
}
