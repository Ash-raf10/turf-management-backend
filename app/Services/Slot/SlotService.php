<?php

namespace App\Services\Slot;

use App\Models\Slot;
use App\Models\Field;
use App\Services\GlobalStatus;
use App\Traits\TransformPaginate;
use Illuminate\Database\Eloquent\Builder;
use App\Services\Slot\InternalSlotService;

class  SlotService
{
    use TransformPaginate;

    public function __construct(protected InternalSlotService $internalSlotService)
    {
    }

    public function slotList(string $fieldId): Builder
    {
        return Slot::where('field_id', $fieldId);
    }

    public function saveSlots(Field $field, array $requestData)
    {
        $slots = $requestData['slot'];
        $fieldId = $field->id;
        $activeStatus = GlobalStatus::getRecordStatus('Active');

        foreach ($slots as $slot) {
            $slot['field_id'] = $fieldId;
            $this->internalSlotService->updateInternalSlotStatus($slot, $activeStatus);

            Slot::create($slot);
        }
    }

    public function updateSlots(Field $field, array $requestData)
    {

        $slots = $requestData['slot'];
        $activeStatus = GlobalStatus::getRecordStatus('Active');

        $this->internalSlotService->updateStatus($field->id, GlobalStatus::getRecordStatus('Inactive'));

        foreach ($slots as $slot) {
            $slot['field_id'] = $field->id;
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

    public function slotInfo(Field $field)
    {
        $startTime = request()->query('start_time');
        $endTime = request()->query('end_time');

        if (!$startTime && !$endTime) {
            return null;
        }

        $slot = $field->slots()->where('start_time', '<=', $startTime)
            ->where('end_time', '>=', $endTime)->first();

        return $slot;
    }
}
