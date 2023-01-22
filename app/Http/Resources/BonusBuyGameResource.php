<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BonusBuyGameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'bonus_buy_id' => $this->bonus_buy_id,
            'stake' => $this->stake,
            'price' => $this->price,
            'result' => $this->result,
            'multiplier' => $this->multiplier,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
