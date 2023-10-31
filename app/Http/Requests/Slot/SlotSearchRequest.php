<?php

namespace App\Http\Requests\Slot;

use App\Services\GlobalType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * SlotRequest
 *
 * @category Class
 * @author   Ashraf <shahdatashraf@10gmail.com>
 */
class SlotSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'type' => ['required', Rule::in(array_keys(GlobalType::getFieldType()))],
            'district' =>  'required|string',
            'start_time' =>  'required|date_format:H:i',
            'end_time' =>  'required|date_format:H:i',
            'date' =>  'required|date_format:Y-m-d',
            'duration' =>  'required|numeric',
        ];
    }
}
