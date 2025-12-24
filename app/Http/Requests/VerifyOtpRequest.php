<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('validation_messages.email_required'),
            'email.email' => __('validation_messages.email_invalid'),
            'otp.required' => __('validation_messages.otp_required'),
            'otp.digits' => __('validation_messages.otp_digits'),
        ];
    }
}
