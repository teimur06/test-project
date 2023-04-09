<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class DeliveryRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "user_name" => $this->user->name,
            "from_city_id" => $this->from_city_id,
            "to_city_id" => $this->to_city_id,
            "from_city_name" => $this->fromCity->name,
            "to_city_name" => $this->toCity->name,
            "delivery_date" => Carbon::parse( $this->delivery_date)->format('Y-m-d'),
            "status" => $this->status,
            "group" => $this->group,
        ];
    }
}
