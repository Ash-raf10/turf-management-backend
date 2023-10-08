<?php

namespace App\Services;

use App\Models\Turf;
use App\Models\Field;
use Illuminate\Database\Eloquent\Builder;

class FieldService
{

    /**
     * get field list of a turf
     *
     * @return Builder
     */
    public function getFieldList(Turf $turf): Builder
    {
        return Field::query()->where('turf_id', $turf->id)->with('creator', 'updator');
    }

    /**
     * saveFieldInfo
     *
     * @param  array $requestData
     * @return Field
     */
    public function saveFieldInfo(Turf $turf, array $requestData): Field
    {
        $field = $turf->fields()->create($requestData);
        $field->load('turf', 'creator', 'updator');

        return $field;
    }

    /**
     * updateTurfInfo
     *
     * @param Field $field
     * @param  array $requestData
     * @return Field
     */
    public function updateFieldInfo(Field $field, array $requestData): Field
    {
        $field->update($requestData);
        $field->load('turf', 'creator', 'updator')->refresh();

        return $field;
    }

    /**
     * soft delete field
     *
     * @param Field $field
     * @return bool
     */
    public function deleteField(Field $field): bool
    {
        $field->record_status = GlobalStatus::getRecordStatus('Deleted');
        $field->save();

        if ($field->delete()) {
            return true;
        }
        return false;
    }
}
