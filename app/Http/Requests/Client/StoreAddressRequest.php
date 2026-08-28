<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fullname' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^0[0-9]{8,10}$/'],
            'province_id' => ['required', 'uuid', 'exists:provinces,id'],
            'ward_id' => ['required', 'uuid', 'exists:wards,id'],
            'address' => ['required', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'fullname' => __('client.account.fields.recipient'),
            'phone_number' => __('client.account.fields.phone_number'),
            'province_id' => __('client.account.fields.province'),
            'ward_id' => __('client.account.fields.ward'),
            'address' => __('client.account.fields.address'),
        ];
    }
}
