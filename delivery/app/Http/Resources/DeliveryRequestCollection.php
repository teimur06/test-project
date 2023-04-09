<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DeliveryRequestCollection extends ResourceCollection
{
    public $collects = DeliveryRequestResource::class;
    /**
     * Transform the resource collection into an array.
     *
     * @return \Illuminate\Support\Collection
     */
    public function toArray(Request $request): \Illuminate\Support\Collection
    {
        return $this->collection;
    }
}
