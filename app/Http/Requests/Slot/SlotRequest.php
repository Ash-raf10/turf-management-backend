<?php

namespace App\Http\Requests\Slot;

use App\Rules\SeparateTimeSlots;
use Illuminate\Foundation\Http\FormRequest;

/**
 * SlotRequest
 *
 * @category Class
 * @author   Ashraf <shahdatashraf@10gmail.com>
 */
class SlotRequest extends FormRequest
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
            'slot' => ['required', 'array', new SeparateTimeSlots],
            'slot.*.id' =>  'required_if:_method,PUT,PATCH|exists:slots,id',
            'slot.*.start_time' =>  'required|string',
            'slot.*.end_time' =>  'required|string',
            'slot.*.type' =>  'required|string',
            'slot.*.price' =>  'required|numeric',
        ];
    }
}
