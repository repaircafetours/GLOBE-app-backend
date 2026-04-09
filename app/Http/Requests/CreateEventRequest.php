<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
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
            "date" => "required|date_format:Y-m-d\\TH:i:s.u\\Z",
            "city" => "required|string",
            "zip_code" => "required|string",
            "address" => "required|string",
            "extra_attributes" => "nullable|object"
        ];
    }
}
