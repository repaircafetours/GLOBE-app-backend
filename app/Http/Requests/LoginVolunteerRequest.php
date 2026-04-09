<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginVolunteerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "login" => ["nullable", "string"],
            "idHumHub" => ["nullable", "integer"],
            "password" => ["required", "string"],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->filled("login") && !$this->filled("idHumHub")) {
                $validator
                    ->errors()
                    ->add(
                        "identifier",
                        "Un identifiant (login ou idHumHub) est requis.",
                    );
            }
        });
    }
}
