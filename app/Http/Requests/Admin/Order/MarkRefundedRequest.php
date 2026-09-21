<?php

namespace App\Http\Requests\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;

class MarkRefundedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'string', 'max:500'],
            'reference' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'note' => __('admin/order.refund.note'),
            'reference' => __('admin/order.refund.reference'),
        ];
    }
}
