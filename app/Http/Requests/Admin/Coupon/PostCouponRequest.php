<?php

namespace App\Http\Requests\Admin\Coupon;

use App\Const\CouponConst;
use App\Const\GlobalConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostCouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => mb_strtoupper(trim((string) $this->input('code'))),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id');
        $isPercent = (int) $this->input('discount_type') === CouponConst::PERCENT;

        return [
            'code' => ['required', 'string', 'max:50', 'regex:/^[A-Z0-9_-]+$/', Rule::unique('coupons', 'code')->ignore($id)],
            'title' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'discount_type' => ['required', 'integer', Rule::in(array_keys(CouponConst::types()))],
            'discount_value' => array_filter([
                'required',
                'numeric',
                'min:1',
                $isPercent ? 'max:100' : 'max:99999999',
            ]),
            'usage_limit' => ['required', 'integer', 'min:1', 'max:65535'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'is_active' => ['required', Rule::in(array_keys(GlobalConst::statuses()))],

            'min_order_value' => ['nullable', 'numeric', 'min:0'],
            'max_discount_value' => ['nullable', 'numeric', 'min:0', Rule::requiredIf($isPercent)],
            'valid_categories' => ['nullable', 'array'],
            'valid_categories.*' => ['uuid', 'exists:categories,id'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.regex' => __('admin/coupon.messages.code_format'),
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
            'code' => __('admin/coupon.fields.code'),
            'title' => __('admin/coupon.fields.title'),
            'description' => __('admin/coupon.fields.description'),
            'discount_type' => __('admin/coupon.fields.discount_type'),
            'discount_value' => __('admin/coupon.fields.discount_value'),
            'usage_limit' => __('admin/coupon.fields.usage_limit'),
            'start_date' => __('admin/coupon.fields.start_date'),
            'end_date' => __('admin/coupon.fields.end_date'),
            'is_active' => __('admin/coupon.fields.is_active'),
            'min_order_value' => __('admin/coupon.fields.min_order_value'),
            'max_discount_value' => __('admin/coupon.fields.max_discount_value'),
            'valid_categories' => __('admin/coupon.fields.valid_categories'),
        ];
    }
}
