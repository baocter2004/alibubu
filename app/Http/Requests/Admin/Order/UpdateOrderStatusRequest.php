<?php

namespace App\Http\Requests\Admin\Order;

use App\Const\OrderConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
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
            'status' => ['required', 'integer', Rule::in(array_keys(OrderConst::statuses()))],
            'cancel_reason' => [
                Rule::requiredIf((int) $this->input('status') === OrderConst::STATUS_CANCELLED),
                'nullable',
                'string',
                'max:255',
            ],
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
            'status' => __('admin/order.fields.status'),
            'cancel_reason' => __('admin/order.fields.cancel_reason'),
        ];
    }
}
