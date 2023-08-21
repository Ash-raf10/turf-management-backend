<?php

namespace App\Http\Requests\Auth;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
        // check if the field contains email or mobile and set as mobile or email
        $identifierType = filter_var($this->email_phone, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        $this->merge([
            'identifier_type' => $identifierType,
        ]);

        // if field is mobile then add +880
        if ($identifierType === 'mobile') {
            $phone = preg_replace('/^(0|88)/', '+880', $this->email_phone, 1);

            $this->merge([
                'email_phone' => $phone,
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
            'email_phone' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (
                        !filter_var($value, FILTER_VALIDATE_EMAIL)
                        && !preg_match('/^(((\+|00)?880)|0)(\d){10}$/', $value)
                    ) {
                        $fail('This must be a valid email or phone number.');
                    }
                },
            ],
            'password' => [
                'required', Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'identifier_type' => 'required'
        ];
    }
}
