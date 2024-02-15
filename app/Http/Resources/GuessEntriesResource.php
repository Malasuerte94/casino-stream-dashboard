<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuessEntriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->name,
            'avatar' => $this->user->profile_photo_path,
            'biggest_multi' => $this->biggest_multi,
            'result' => $this->estimated,
            'game_winner' => $this->game_winner,
            'lowest_multi' => $this->lowest_multi,
        ];
    }
}
