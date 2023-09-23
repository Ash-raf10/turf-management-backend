<?php

namespace App\Services\Slot;

use App\Http\Resources\Slot\SlotResource;
use App\Models\Slot;
use App\Models\InternalSlot;
use App\Services\GlobalStatus;
use App\Services\Slot\InternalSlotService;
use App\Traits\TransformPaginate;

class  SlotService
{
    use TransformPaginate;

    public function __construct(protected InternalSlotService $internalSlotService)
    {
    }

    public function slotList(string $fieldId): array
    {
        $query =  Slot::where('field_id', $fieldId);
        $paginateData = $this->paginateData($query);
        $slots =  SlotResource::collection($paginateData);

        return $this->transformPaginateData($slots);
    }

    public function saveSlots(array $requestData)
    {
        $slots = $requestData['slot'];
        $fieldId = $requestData['field_id'];
        $activeStatus = GlobalStatus::getRecordStatus('Active');

        foreach ($slots as $slot) {
            $slot['field_id'] = $fieldId;
            $this->internalSlotService->updateInternalSlotStatus($slot, $activeStatus);

            Slot::create($slot);
        }
    }

    public function updateSlots(array $requestData)
    {

        $slots = $requestData['slot'];
        $fieldId = $requestData['field_id'];
        $activeStatus = GlobalStatus::getRecordStatus('Active');

        $this->internalSlotService->updateStatus($fieldId, GlobalStatus::getRecordStatus('Inactive'));

        foreach ($slots as $slot) {
            $slot['field_id'] = $fieldId;
            Slot::where('id', $slot['id'])->update($slot);
            $this->internalSlotService->updateInternalSlotStatus($slot, $activeStatus);
        }
    }

    public function deleteSlot(Slot $slot)
    {
        $slotArray['field_id'] = $slot->field_id;
        $slotArray['start_time'] = $slot->start_time;
        $slotArray['end_time'] = $slot->end_time;
        $inactiveStatus = GlobalStatus::getRecordStatus('Inactive');

        $this->internalSlotService->updateInternalSlotStatus($slotArray, $inactiveStatus);

        return  $slot->delete();
    }
}
