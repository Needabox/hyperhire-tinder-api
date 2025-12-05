<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LikedPersonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'person_id' => $this->user->id,
            'name' => $this->user->name,
            'age' => $this->user->age,
            'pictures' => $this->user->pictures->pluck('picture_url')->toArray(),
            'liked_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

