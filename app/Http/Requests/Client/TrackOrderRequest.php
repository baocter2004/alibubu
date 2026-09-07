<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class TrackOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', 'regex:/^ORD[0-9A-Z_-]+$/i'],
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^0[0-9]{8,10}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.regex' => __('client.tracking.messages.invalid_code'),
            'phone_number.regex' => __('client.tracking.messages.invalid_phone'),
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => __('client.tracking.fields.code'),
            'phone_number' => __('client.tracking.fields.phone_number'),
        ];
    }
}
