<?php

namespace App\Http\Controllers\Api\V1\Documents;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\ImageRequest;
use App\Services\Document\ImageService;

class ImageController extends BaseController
{
    public function __construct(protected ImageService $imageService)
    {
    }

    public function store(ImageRequest $request)
    {
        $imageResponse =  $this->imageService->storeImage($request->validated());

        return $this->sendResponse($imageResponse->success, "", $imageResponse->msg);
    }
}
