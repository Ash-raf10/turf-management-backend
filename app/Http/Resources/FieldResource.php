<?php

namespace App\Http\Resources;

use App\Http\Resources\Slot\SlotResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FieldResource extends JsonResource
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
            "name" => $this->name,
            "field_type" => $this->field_type,
            "description" => $this->description,
            'record_status' => $this->record_status,
            'turf' => new TurfResource($this->whenLoaded('turf')),
            'slots' => SlotResource::collection($this->whenLoaded('slots')),
            'slot_info' => new SlotResource($this->whenNotNull($this->slotInfo)),
            'created_by' => new UuidNameResource($this->whenLoaded('creator')),
            'updated_by' =>  new UuidNameResource($this->whenLoaded('updator')),
            'created_at' => $this->created_at->format('d-F-Y'),
            'updated_at' => $this->updated_at->format('d-F-y')
        ];
    }
}
