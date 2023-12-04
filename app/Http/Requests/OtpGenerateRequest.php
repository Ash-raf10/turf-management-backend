<?php

namespace App\Http\Requests;

use App\Services\GlobalType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * OtpRequest
 *
 * @category Class
 * @author   Ashraf <shahdatashraf@10gmail.com>
 */
class OtpGenerateRequest extends FormRequest
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // if mobile field is present then check if it already contains +880
        if ($this->mobile && strpos($this->mobile, "+880") !== 0) {
            // if not then add +880
            $phone = preg_replace('/^(0|88)/', '+880', $this->mobile, 1);


            $this->merge([
                'mobile' => $phone,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => 'required_without:mobile|email|max:255|unique:users',
            'mobile' =>  [
                'required_without:email', 'string', 'regex:/^(((\+|00)?880)|0)(\d){10}$/',
                'max:20', 'min:11', 'unique:users,mobile,except,id'  //  mobile number should be unique
            ],
            'otp_type' => ['required', Rule::in(array_keys(GlobalType::getOtpType()))]
        ];
    }
}
