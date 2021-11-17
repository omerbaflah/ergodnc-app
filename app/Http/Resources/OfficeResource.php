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
            'title' => $this->title,
            'description' => $this->description,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'address_line1' => $this->address_line1,
            'approval_status' => $this->approval_status,
            'hidden' => $this->hidden,
            'price_per_day' => $this->price_per_day,
            'monthly_discount' => $this->monthly_discount,
            'user' => UsersListResource::make($this->user)
        ];
    }
}
