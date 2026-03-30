<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitorRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title" => "required|string",
            "name" => "required|string",
            "surname" => "required|string",
            "zip_code" => "required|string",
            "city" => "required|string",
            "phone_number" => "required|string",
            "source" => "required|string",
            "notification" => "boolean",
            "email" => "required|email",
            "extra_attributes" => "nullable|object",
        ];
    }
}
