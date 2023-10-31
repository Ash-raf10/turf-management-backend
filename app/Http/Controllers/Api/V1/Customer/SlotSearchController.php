<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Slot\SlotSearchRequest;
use App\Services\Customer\SlotSearchService;

class SlotSearchController extends BaseController
{

    public function __construct(protected SlotSearchService $slotSearchService)
    {
    }

    public function search(SlotSearchRequest $request)
    {
        $this->slotSearchService->setSearchData($request->validated());

        $query = $this->slotSearchService->internalBookingQuery($request->validated());

        $internalBookingRecordsArray = $this->slotSearchService->internalBookingRecordsArray($query);

        $slots = $this->slotSearchService->internalSlotBasedOnSearch()
            ->where('record_status', 'Active')->whereNotIn('id', $internalBookingRecordsArray)->get();

        $generatedSlots = $this->slotSearchService->generateSlots($slots);

        return $this->sendResponse(true, $generatedSlots);
    }
}
