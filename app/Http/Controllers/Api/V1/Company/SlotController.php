<?php

namespace App\Http\Controllers\Api\V1\Company;

use Exception;
use App\Models\Slot;
use App\Models\Field;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\InternalBooking;
use App\Services\Slot\SlotService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Slot\SlotRequest;
use App\Services\Slot\InternalSlotService;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\FieldResource;
use App\Http\Resources\Slot\SlotResource;

class SlotController extends BaseController
{
    public function __construct(
        protected SlotService $slotService,
        protected InternalSlotService $internalSlotService
    ) {
    }

    public function index(Field $field)
    {
        $slotQuery = $this->slotService->slotList($field->id);
        [$slotData, $pagination] = $this->paginateData($slotQuery);

        $data = SlotResource::collection($slotData);

        return $this->sendPaginationResponse($data, $pagination);
    }

    public function store(Field $field, SlotRequest $request)
    {
        try {
            $slotCheck = Slot::where('field_id', $field->id)->get();
            if ($slotCheck->count() !== 0) {
                return $this->sendResponse(false, "", __("Slot already exist"), 404, 4001);
            }

            DB::beginTransaction();
            $this->internalSlotService->saveSlotIntervals($field->id);
            $this->slotService->saveSlots($field, $request->validated());
            DB::commit();

            return $this->sendResponse(true, "", __("Successflly created"), 201, 0000);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return $this->sendResponse(false, "", __("Something went wrong"), 404, 4001);
        }
    }

    public function update(Field $field, SlotRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->slotService->updateSlots($field, $request->validated());
            DB::commit();

            return $this->sendResponse(true, "", __("Successflly updated"), 200, 0000);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return $this->sendResponse(false, "", __("Something went wrong"), 404, 4001);
        }
    }

    public function delete(Slot $slot)
    {
        try {
            DB::beginTransaction();
            $delete = $this->slotService->deleteSlot($slot);
            if (!$delete) {
                DB::rollBack();
            }
            DB::commit();

            return $this->sendResponse(true, "", __("Successflly deleted"), 200, 0000);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return $this->sendResponse(false, "", __("Something went wrong"), 404, 4001);
        }
    }

    public function info(Field $field)
    {
        $slot = $this->slotService->slotInfo($field);

        $field->slotInfo = $slot;

        return $this->sendResponse(true, new FieldResource($field), "", 200);
    }

    public function book(Request $request)
    {
        $selectedSlot = $this->internalSlotService->newInternalSlotsBetweenTimeRangeQuery(
            $request->start_time,
            $request->end_time,
            $request->field_id
        )->get();

        $slotPrice = Slot::where('field_id', $request->field_id)
            ->whereTime('start_time', '<=', $request->start_time)->whereTime('end_time', '>=', $request->end_time)
            ->first();

        $data['date'] = $request->date;
        $data['status'] = "Locked";

        foreach ($selectedSlot as $slot) {
            $data['internal_slot_id'] = $slot->id;
            $data['time'] = $slot->time;

            InternalBooking::create($data);
        }

        $data['field_id'] = $request->field_id;
        $data['start_time'] = $request->start_time;
        $data['end_time'] = $request->end_time;
        $data['booked_by'] = auth()->user()->id;

        Booking::create($data);
    }
}
