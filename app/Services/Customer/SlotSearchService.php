<?php

namespace App\Services\Customer;

use App\Models\InternalBooking;
use App\Services\Slot\InternalSlotService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SlotSearchService
{
    private $searchData;
    private $startTime;
    private $endTime;
    private $date;
    private $district;
    private $type;
    private $duration;

    public function __construct(protected InternalSlotService $internalSlotService)
    {
    }

    public function setSearchData(array $requestData)
    {
        $this->searchData = $requestData;
        $this->startTime = $requestData['start_time'];
        $this->endTime = $requestData['end_time'];
        $this->date = $requestData['date'];
        $this->district = $requestData['district'];
        $this->duration = $requestData['duration'];
        $this->type = $requestData['type'];
    }

    public function internalBookingQuery(): Builder
    {
        if ($this->startTime > $this->endTime) {
            $query = InternalBooking::where('time', '>=', $this->startTime)
                ->orWhere('time', '>=', "00:00")
                ->where('time', '<=', $this->endTime);
        } else {
            $query = InternalBooking::where('time', '>=', $this->startTime)
                ->where('time', '<=', $this->endTime);
        }

        $query->where('district', $this->district)->where('field_type', $this->type);

        return $query;
    }

    public function internalBookingRecordsArray(Builder $query): array
    {
        return $query->whereDate('date', $this->date)
            ->pluck('internal_slot_id')->toArray();
    }

    public function internalSlotBasedOnSearch()
    {
        return $this->internalSlotService->internalSlotBasedOnSearch($this->searchData);
    }

    public function generateSlots(Collection $slots)
    {
        $slots = $this->internalSlotService->groupBySlot($slots);
        $div = $this->duration / 30;

        return $this->internalSlotService->generateSlots($slots, $div);
    }
}
