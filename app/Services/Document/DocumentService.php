<?php

namespace App\Services\Document;

use Exception;
use App\Services\GlobalType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    private array $documentType;

    /**
     * setDocumentType
     *
     * @param  string $key
     * @return array
     */
    public function setDocumentType(string $key): array
    {
        $this->documentType =  GlobalType::getDocumentType($key);

        return $this->documentType;
    }

    /**
     * getDocumentTypeEntity
     *
     * @param  string $id
     * @return ?Model
     */
    public function getDocumentTypeEntity(string $id): ?Model
    {
        $model = $this->documentType['model'];

        return $model::query()->where('id', $id)->first();
    }

    /**
     * documentInfo
     *
     * @param  UploadedFile $file
     * @param  string $turfId
     * @return array
     */
    public function documentInfo(UploadedFile $file, string $turfId): array
    {
        $filePath =  $this->documentType['file_path'] . "/$turfId";
        $fileType = $file->extension();
        $fileName = uniqid() . '.' . $fileType;

        return [$fileName, $filePath, $fileType];
    }

    /**
     * documentUpload
     *
     * @param  UploadedFile $file
     * @param  string $filePath
     * @param  string $fileName
     * @return bool
     */
    public function documentUpload(UploadedFile $file, string $filePath, string $fileName): bool
    {
        try {
            $success = Storage::putFileAs($filePath, $file, $fileName);

            return $success;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * deleteDocument
     *
     * @param  string $filePath
     * @param  string $fileName
     * @return bool
     */
    public function deleteDocument(string $filePath, string $fileName): bool
    {
        $fullFilePath = $filePath . "/" . $fileName;
        if (Storage::exists($fullFilePath)) {
            Storage::delete($fullFilePath);
            Log::info("File $fullFilePath deleted");

            return true;
        }
        Log::info("File $fullFilePath does not exist");

        return false;
    }
}
