<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfficeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'distance' => $this->distance,
            'address_line1' => $this->address_line1,
            'approval_status' => $this->approval_status,
            'hidden' => $this->hidden,
            'price_per_day' => $this->price_per_day,
            'monthly_discount' => $this->monthly_discount,
            'reservations_count' => $this->reservations_count,
            'user' => UsersListResource::make($this->user),
            'images' => ImageResource::collection($this->images),
            'tags' => TagResource::collection($this->tags),
        ];
    }
}
