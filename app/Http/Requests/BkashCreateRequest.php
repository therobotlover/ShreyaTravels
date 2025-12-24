<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BkashCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'integer', 'exists:bookings,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => __('validation_messages.booking_id_required'),
            'booking_id.integer' => __('validation_messages.booking_id_invalid'),
            'booking_id.exists' => __('validation_messages.booking_id_missing'),
        ];
    }
}
