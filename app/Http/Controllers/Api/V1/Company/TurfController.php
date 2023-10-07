<?php

namespace App\Http\Controllers\Api\V1\Company;

use Exception;
use App\Services\TurfService;
use App\Http\Requests\TurfRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\TurfResource;

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
}
