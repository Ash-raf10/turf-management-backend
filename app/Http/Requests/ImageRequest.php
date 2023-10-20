<?php

namespace App\Http\Requests;

use App\Services\GlobalType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "type" => ['required', Rule::in(array_keys(GlobalType::getDocumentType()))],
            "id" => "required",
            "images" => "required|array|max:5|min:1",
            "images.*.file" => "required|file|max:10240",
            "images.*.note" => "nullable|max:1000",
        ];
    }
}
