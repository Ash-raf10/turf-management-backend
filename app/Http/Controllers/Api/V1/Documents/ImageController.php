<?php

namespace App\Http\Controllers\Api\V1\Documents;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\ImageRequest;
use App\Models\Image;
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

    public function destroy(Image $image)
    {
        $imageDestroyResponse = $this->imageService->deleteImage($image);

        return $this->sendResponse($imageDestroyResponse->success, "", $imageDestroyResponse->msg);
    }
}
