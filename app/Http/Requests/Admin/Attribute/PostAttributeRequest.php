<?php

namespace App\Http\Requests\Admin\Attribute;

use App\Const\GlobalConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostAttributeRequest extends FormRequest
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
            'values' => array_values(array_filter(
                $this->input('values', []),
                fn ($v) => ! empty($v['value'])
            )),
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

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('attributes', 'name')->ignore($id)],
            'is_active' => ['required', Rule::in(array_keys(GlobalConst::statuses()))],

            'values' => ['required', 'array', 'min:1', 'max:30'],
            'values.*.id' => ['nullable', 'integer', 'exists:attribute_values,id'],
            'values.*.value' => ['required', 'string', 'max:255'],
            'values.*.is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $seen = [];

            foreach ($this->input('values', []) as $index => $value) {
                $key = mb_strtolower(trim((string) ($value['value'] ?? '')));

                if ($key === '') {
                    continue;
                }

                if (in_array($key, $seen, true)) {
                    $validator->errors()->add("values.$index.value", __('admin/attribute.messages.duplicate_value'));
                }

                $seen[] = $key;
            }
        });
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('admin/attribute.fields.name'),
            'is_active' => __('admin/attribute.fields.is_active'),
            'values' => __('admin/attribute.fields.values'),
            'values.*.value' => __('admin/attribute.fields.value'),
        ];
    }
}
