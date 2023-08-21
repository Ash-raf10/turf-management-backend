<?php

namespace App\Http\Requests\Auth;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class CustomerUserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:30',
            'email' => 'required|email|max:255|unique:users',
            'password' => [
                'required', Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'mobile' => [
                'required', 'string', 'regex:/^(((\+|00)?880)|0)(\d){10}$/',
                'max:20', 'min:11', 'unique:users,mobile,except,id'  //  mobile number should be unique
            ],
            'city' => 'required|string|max:255'
        ];
    }
}
