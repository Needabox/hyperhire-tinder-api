<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecommendedPeopleRequest extends FormRequest
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
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'lat.required' => 'Latitude (lat) is required.',
            'lat.numeric' => 'Latitude must be a number.',
            'lat.between' => 'Latitude must be between -90 and 90.',
            'lng.required' => 'Longitude (lng) is required.',
            'lng.numeric' => 'Longitude must be a number.',
            'lng.between' => 'Longitude must be between -180 and 180.',
            'page.integer' => 'Page must be an integer.',
            'page.min' => 'Page must be at least 1.',
            'per_page.integer' => 'Per page must be an integer.',
            'per_page.min' => 'Per page must be at least 1.',
            'per_page.max' => 'Per page cannot exceed 100.',
        ];
    }

    /**
     * Get validated data with defaults.
     *
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        return [
            'page' => $validated['page'] ?? 1,
            'per_page' => $validated['per_page'] ?? 20,
            'lat' => (float) $validated['lat'],
            'lng' => (float) $validated['lng'],
        ];
    }
}

