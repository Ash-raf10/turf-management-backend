<?php

namespace App\Http\Controllers\Api\V1\Company;

use Exception;
use App\Services\TurfService;
use App\Http\Requests\TurfRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\TurfResource;
use App\Models\Turf;

class TurfController extends BaseController
{
    public function __construct(private TurfService $turfService)
    {
    }

    public function index()
    {
        $turfQuery = $this->turfService->getTurfList();

        [$turfData, $pagination] = $this->paginateData($turfQuery);

        return $this->sendPaginationResponse(TurfResource::collection($turfData), $pagination);
    }

    public function show(Turf $turf)
    {
        $turf->load('company', 'creator', 'updator');

        return $this->sendResponse(true, new TurfResource($turf));
    }

    public function store(TurfRequest $request)
    {
        try {
            $turf = $this->turfService->saveTurfInfo($request->validated());

            return $this->sendResponse(true, new TurfResource($turf), __("Successfully created"), 200);
        } catch (Exception $e) {
            Log::error($e);

            return $this->sendResponse(false, "", __("Something Went Wrong"), 404, 4001);
        }
    }

    public function update(Turf $turf, TurfRequest $request)
    {
        try {
            $turf = $this->turfService->updateTurfInfo($turf, $request->validated());

            return $this->sendResponse(true, new TurfResource($turf), __("Successfully updated"), 200);
        } catch (Exception $e) {
            Log::error($e);

            return $this->sendResponse(false, "", __("Something Went Wrong"), 404, 4001);
        }
    }

    public function destroy(Turf $turf)
    {
        $result = false;
        try {
            $result = $this->turfService->deleteTurf($turf);
        } catch (Exception $e) {
            Log::error($e);
        }

        if ($result) {
            return $this->sendResponse(true, "", __("Successfully deleted"), 200);
        }
        return $this->sendResponse(false, "", __("Something Went Wrong"), 404, 4001);
    }
}
