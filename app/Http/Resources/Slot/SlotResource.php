<?php

namespace App\Http\Resources\Slot;

use App\Http\Resources\CreatorUpdatorResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SlotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'field_id' => $this->field_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'type' => $this->type,
            'discount' => $this->discount,
            'price' => $this->price,
            'record_status' => $this->record_status,
            'creator' => new CreatorUpdatorResource($this->whenLoaded('creator')),
            'updator' => new CreatorUpdatorResource($this->whenLoaded('updator')),
            'created_at' => $this->created_at->format('d-F-Y'),
            'updated_at' => $this->updated_at->format('d-F-y')
        ];
    }
}
