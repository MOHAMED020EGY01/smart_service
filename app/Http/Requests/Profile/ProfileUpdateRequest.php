<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            "name"      => ["nullable","string","max:255"],
            "phone"     => ["nullable","numeric"],
            "Category"  => ["nullable","string","max:255"],
            "Expert"    => ["nullable","string","max:255"],

            "city"              => ["nullable","string","max:255"],
            "street"            => ["nullable","string","max:255"],
            "address_in_details"=> ["nullable","string","max:255"],
        ];
    }
}
