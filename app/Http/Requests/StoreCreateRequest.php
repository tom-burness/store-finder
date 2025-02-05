<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// NOTE: I would normally call this a Store request but StoreStoreRequest is...odd

/**
 * @property float $latitude
 * @property float $longitude
 */
class StoreCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'status' => 'required|in:open,closed',
            'type' => 'required|in:takeaway,shop,restaurant',
            'latitude' => 'required|decimal:1,8|between:-90,90',
            'longitude' => 'required|decimal:1,8|between:-180,180',
            'max_delivery_distance' => 'required|integer|min:1',
        ];
    }

    /**
     * @return array<string,string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The store name is required.',
            'status.required' => 'The store status is required.',
            'status.in' => 'The status must be either open or closed.',
            'type.required' => 'The store type is required.',
            'type.in' => 'The type must be either takeaway, shop, or restaurant.',
            'latitude.required' => 'Latitude is required.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.required' => 'Longitude is required.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
            'max_delivery_distance.required' => 'Max distance is required.',
            'max_delivery_distance.min' => 'Max distance must be at least 1.',
        ];
    }
}
