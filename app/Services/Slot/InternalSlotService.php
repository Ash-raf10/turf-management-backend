<?php

namespace App\Services\Slot;

use Illuminate\Support\Str;
use App\Models\InternalSlot;
use App\Services\GlobalStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class InternalSlotService
{
    public function saveSlotIntervals(string $fieldId)
    {
        $checkPreviousExistance = InternalSlot::where('field_id', $fieldId)->get();

        if ($checkPreviousExistance->count() === 0) {
            $slots = $this->generateSlotIntervals();

            foreach ($slots as $in => $slot) {
                $data[$in]['id'] = Str::orderedUuid()->toString();
                $data[$in]['field_id'] = $fieldId;
                $data[$in]['time'] = $slot;
                $data[$in]['sequence'] = $in;
                $data[$in]['record_status'] = GlobalStatus::getRecordStatus('Inactive');
                $data[$in]['created_at'] = now();
                $data[$in]['updated_at'] = now();
            }

            InternalSlot::insert($data);
        }
    }

    private function generateSlotIntervals(int $intervalSecond = 1800)
    {
        $intervals = [];
        $start_time = strtotime('00:00'); // Start at midnight (00:00)

        while ($start_time < strtotime('24:00')) {
            $intervals[] = date('H:i', $start_time); // Add the formatted time to the intervals array
            $start_time += $intervalSecond;
        }

        return $intervals;
    }

    public function updateInternalSlotStatus(array $slot, string $status)
    {
        $fieldId = $slot['field_id'];
        $startTime = $slot['start_time'];
        $endTime = $slot['end_time'];

        $internalSlots = $this->newInternalSlotsBetweenTimeRangeQuery($startTime, $endTime, $fieldId);
        $internalSlots->update(['record_status' => $status]);
    }

    public function internalSlotsBetweenTimeRangeQuery(string $startTime, string $endTime, string $fieldId): Builder
    {
        if ($startTime > $endTime) {
            $query = InternalSlot::where('field_id', $fieldId)
                ->where('time', '>=', $startTime)
                ->orWhere('time', '>=', "00:00")
                ->where('time', '<=', $endTime);
        } else {
            $query = InternalSlot::where('field_id', $fieldId)
                ->where('time', '>=', $startTime)
                ->where('time', '<=', $endTime);
        }

        return $query;
    }

    public function newInternalSlotsBetweenTimeRangeQuery(
        string $startTime,
        string $endTime,
        string $fieldId = null
    ): Builder {
        if ($startTime > $endTime) {
            $query = InternalSlot::where('time', '>=', $startTime)
                ->orWhere('time', '>=', "00:00")
                ->where('time', '<', $endTime);
        } else {
            $query = InternalSlot::where('time', '>=', $startTime)
                ->where('time', '<', $endTime);
        }

        if ($fieldId) {
            $query->where('field_id', $fieldId);
        }

        return $query;
    }

    public function updateStatus(string $fieldId, string $status)
    {
        InternalSlot::where('field_id', $fieldId)->update([
            'record_status' => $status
        ]);
    }
}
