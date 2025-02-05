<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'postcode' => 'required|string'
        ];
    }

    /**
     * @return array<string,string>
     */
    public function messages(): array
    {
        return [
            'postcode.required' => 'Postcode parameter is required',
            'postcode.string' => 'Postcode must be a string'
        ];
    }
}
