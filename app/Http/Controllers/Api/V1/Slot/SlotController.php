<?php

namespace App\Http\Controllers\Api\V1\Slot;

use DateTime;
use Exception;
use DateInterval;
use App\Models\Slot;
use App\Models\SlotNew;
use Illuminate\Support\Str;
use App\Models\InternalSlot;
use Illuminate\Http\Request;
use App\Services\Slot\SlotService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Slot\SlotRequest;
use App\Services\Slot\InternalSlotService;
use App\Http\Controllers\Api\V1\BaseController;

class SlotController extends BaseController
{
    public function __construct(
        protected SlotService $slotService,
        protected InternalSlotService $internalSlotService
    ) {
    }

    public function index()
    {
        [$slots, $pagination] = $this->slotService->slotList(request()->query('field_id'));

        return $this->sendResponse(true, $slots, __("Successflly created"), 201, 0000, $pagination);
    }

    public function save(SlotRequest $request)
    {
        try {
            $slotCheck = Slot::where('field_id', $request->field_id)->get();
            if ($slotCheck->count() !== 0) {
                return $this->sendResponse(false, "", __("Slot already exist"), 404, 4001);
            }

            DB::beginTransaction();
            $this->internalSlotService->saveSlotIntervals($request->field_id);
            $this->slotService->saveSlots($request->validated());
            DB::commit();

            return $this->sendResponse(true, "", __("Successflly created"), 201, 0000);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return $this->sendResponse(false, "", __("Something went wrong"), 404, 4001);
        }
    }

    public function update(SlotRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->slotService->updateSlots($request->validated());
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
}
