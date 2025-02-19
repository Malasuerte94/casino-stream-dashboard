<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => (str_contains($this->profile_photo_path, 'googleusercontent') ? $this->profile_photo_path :
                env('APP_URL') . '/storage/' . $this->profile_photo_path),
            'team' => $this->team?->name
        ];
    }
}
