<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tour_id' => ['required', 'integer', 'exists:tours,id'],
            'travel_date' => ['required', 'date', 'after_or_equal:today'],
            'travelers' => ['required', 'integer', 'min:1', 'max:20'],
            'note' => ['nullable', 'string', 'max:500'],
            'discount_code' => ['nullable', 'string', 'max:50'],
            'intent' => ['required', 'in:pay,hold'],
        ];
    }

    public function messages(): array
    {
        return [
            'tour_id.required' => __('validation_messages.tour_required'),
            'tour_id.integer' => __('validation_messages.tour_invalid'),
            'tour_id.exists' => __('validation_messages.tour_missing'),
            'travel_date.required' => __('validation_messages.travel_date_required'),
            'travel_date.date' => __('validation_messages.travel_date_invalid'),
            'travel_date.after_or_equal' => __('validation_messages.travel_date_future'),
            'travelers.required' => __('validation_messages.travelers_required'),
            'travelers.integer' => __('validation_messages.travelers_invalid'),
            'travelers.min' => __('validation_messages.travelers_min'),
            'travelers.max' => __('validation_messages.travelers_max'),
            'note.max' => __('validation_messages.note_max'),
            'discount_code.max' => __('validation_messages.discount_max'),
            'intent.required' => __('validation_messages.intent_required'),
            'intent.in' => __('validation_messages.intent_invalid'),
        ];
    }
}
