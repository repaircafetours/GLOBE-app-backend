<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitorRequest extends FormRequest
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
            "title" => "nullable|string",
            "name" => "nullable|string",
            "surname" => "nullable|string",
            "zip_code" => "nullable|string",
            "city" => "nullable|string",
            "phone_number" => "nullable|string",
            "source" => "nullable|string",
            "notification" => "nullable|boolean",
            "email" => "nullable|email",
            "extra_attributes" => "nullable|object",
        ];
    }
}
