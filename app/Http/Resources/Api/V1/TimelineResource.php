<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimelineResource extends JsonResource
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
            'description'     => $this->description,
            'start_date'      => $this->start_date ? $this->start_date->toDateString() : null,
            'end_date'        => $this->end_date ? $this->end_date->toDateString() : null,
            'location'        => $this->location,
            'color'           => $this->color,
            'icon'            => $this->icon,
            'status'          => $this->status,
            'sort_order'      => (int) $this->sort_order,
            'timeline_status' => $this->timeline_status,
            'created_at'      => $this->created_at?->toISOString(),
            'updated_at'      => $this->updated_at?->toISOString(),
        ];
    }
}
