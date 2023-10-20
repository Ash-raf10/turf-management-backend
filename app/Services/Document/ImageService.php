<?php

namespace App\Services\Document;

use Exception;
use App\Models\Image;
use RuntimeException;
use App\Traits\InternalResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\InternalResponseObject;
use Illuminate\Database\Eloquent\Model;

class ImageService extends DocumentService
{
    use InternalResponse;

    /**
     * storeImage
     *
     * @param  array $requestData
     * @return InternalResponseObject
     */
    public function storeImage(array $requestData): InternalResponseObject
    {
        try {
            DB::beginTransaction();
            Log::info("requestData", $requestData);
            $documentType = $this->setDocumentType($requestData['type']);
            Log::info("documentTyoe", $documentType);
            $entity = $this->getDocumentTypeEntity($requestData['id']);
            Log::info("entity" . json_encode($entity));

            if ($entity) {
                $this->validateImage($requestData['images'], $entity, $documentType['max_count']);
                $successCount =  $this->imageData($requestData['images'], $entity);
                Log::info("successCount - $successCount");
                DB::commit();
                return $this->response(true, "", __("succssfully uploaded files - $successCount"));
            }

            return $this->response(false, "", __("can not upload file for", $documentType));
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);

            return $this->response(false, "", $e->getMessage());
        }
    }

    /**
     * imageData
     *
     * @param  array $imageData
     * @param  Model $entity
     * @return int
     */
    public function imageData(array $imageData, Model $entity): int
    {
        $successCount = 0;

        foreach ($imageData as $image) {
            [$fileName, $filePath, $fileType] = $this->documentInfo($image['file'], $entity->id);

            $imageEntity = new Image();
            $imageEntity->filename = $fileName;
            $imageEntity->file_path = $filePath;
            $imageEntity->file_type = $fileType;
            $imageEntity->note = $image['note'];
            Log::info("ImageEntityData -" . json_encode($imageEntity));

            $uploadResult = $this->documentUpload($image['file'], $filePath, $fileName);
            Log::info("uploadResult -" . json_encode($uploadResult));

            if (!$uploadResult) {
                continue;
            }

            $result = $this->saveImageData($imageEntity, $entity);
            Log::info("result -" . json_encode($result));

            if (!$result) {
                $deleteDoc = $this->deleteDocument($filePath, $fileName);
                Log::info("deleteDoc - $fileName -" . json_encode($fileName));
            } else {
                $successCount++;
            }
        }
        Log::info("successCount -" . json_encode($successCount));

        return $successCount;
    }

    /**
     * saveImageData
     *
     * @param  Image $imageEntity
     * @param  Model $entity
     * @return bool
     */
    public function saveImageData(Image $imageEntity, Model $entity): bool
    {
        $image = $entity->images()->save($imageEntity);

        return $image ? true : false;
    }

    /**
     * validateImage
     *
     * @param  array &$images
     * @param  Model $entity
     * @param  int $maxCount
     * @return void
     */
    public function validateImage(array &$images, Model $entity, int $maxCount = 5): void
    {
        $uploadedImagesCount = $entity->images()->count();
        Log::info("Already uploadedImagesCount- $uploadedImagesCount");

        $uploadableCount = $maxCount - $uploadedImagesCount;
        Log::info("uploadableCount- $uploadableCount");

        foreach ($images as $index => $image) {
            Log::info("index - $index");
            if ($index >= $uploadableCount) {
                unset($images[$index]);
                continue;
            }
        }
    }

    /**
     * deleteImage
     *
     * @param  Image $image
     * @return InternalResponseObject
     */
    public function deleteImage(Image $image): InternalResponseObject
    {
        try {
            DB::beginTransaction();
            $fileName = $image->filename;
            $filePath = $image->file_path;
            Log::info("File Path $filePath");
            Log::info("File Name $fileName");

            if ($image->delete()) {
                $result = $this->deleteDocument($filePath, $fileName);
                Log::info("delete result -" . json_encode($result));

                if (!$result) {
                    throw new RuntimeException("$filePath/$fileName not deleted");
                }
                DB::commit();

                return $this->response(true, "", __("File deleted succesfully"));
            }
        } catch (RuntimeException $e) {
            Log::info($e);
            DB::rollBack();

            return $this->response(false, "", $e->getMessage());
        }
    }
}
