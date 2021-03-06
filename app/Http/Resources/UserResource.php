<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getRouteKey(),
            'name' => $this->name,
            'placeholder' => $this->thumbnail,
            'media' => $this->media()->count(),
            'views' => $this->views,
            'created_at' => $this->created_at,
            'relationships' => [
                'media' => MediaResource::collection($this->whenLoaded('media')),
            ],
        ];
    }
}
