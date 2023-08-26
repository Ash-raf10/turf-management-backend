<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * OtpRequest
 *
 * @category Class
 * @author   Ashraf <shahdatashraf@10gmail.com>
 */
class OtpRequest extends FormRequest
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
            //
            'otp' =>
            [Rule::requiredIf(fn () => request()->routeIs('otp')), 'numeric', 'digits:6'],
            'token' => 'required|uuid',
        ];
    }
}
