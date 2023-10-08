<?php

namespace App\Http\Controllers\Api\V1\Company;

use Exception;
use App\Models\Turf;
use App\Models\Field;
use App\Services\FieldService;
use App\Http\Requests\FieldRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\TurfResource;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\FieldResource;

class FieldController extends BaseController
{
    public function __construct(private FieldService $fieldService)
    {
    }

    public function index(Turf $turf)
    {
        $fieldQuery = $this->fieldService->getFieldList($turf);

        [$fieldData, $pagination] = $this->paginateData($fieldQuery);

        $data['turf'] = new TurfResource($turf);
        $data['fields'] = FieldResource::collection($fieldData);

        return $this->sendPaginationResponse($data, $pagination);
    }

    public function show(Field $field)
    {
        $field->load('turf', 'creator', 'updator');

        return $this->sendResponse(true, new FieldResource($field));
    }

    public function store(Turf $turf, FieldRequest $request)
    {
        try {
            $field = $this->fieldService->saveFieldInfo($turf, $request->validated());

            return $this->sendResponse(true, new FieldResource($field), __("Successfully created"), 200);
        } catch (Exception $e) {
            Log::error($e);

            return $this->sendResponse(false, "", __("Something Went Wrong"), 404, 4001);
        }
    }

    public function update(Field $field, FieldRequest $request)
    {
        try {
            $field = $this->fieldService->updateFieldInfo($field, $request->validated());

            return $this->sendResponse(true, new FieldResource($field), __("Successfully updated"), 200);
        } catch (Exception $e) {
            Log::error($e);

            return $this->sendResponse(false, "", __("Something Went Wrong"), 404, 4001);
        }
    }

    public function destroy(Field $field)
    {
        $result = false;
        try {
            $result = $this->fieldService->deleteField($field);
        } catch (Exception $e) {
            Log::error($e);
        }

        if ($result) {
            return $this->sendResponse(true, "", __("Successfully deleted"), 200);
        }
        return $this->sendResponse(false, "", __("Something Went Wrong"), 404, 4001);
    }
}
