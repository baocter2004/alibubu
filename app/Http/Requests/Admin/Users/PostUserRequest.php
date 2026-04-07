<?php

namespace App\Http\Requests\Admin\Users;

use App\Const\UserConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $addresses = collect($this->input('user_addresses', []));

            $activeCount = $addresses->where('is_default', true)->count();

            if ($activeCount > 1) {
                $validator->errors()->add(
                    'user_addresses',
                    'Chỉ được có 1 địa chỉ active'
                );
            }

            if ($addresses->isNotEmpty() && $activeCount === 0) {
                $validator->errors()->add(
                    'user_addresses',
                    'Phải có 1 địa chỉ active'
                );
            }
        });
    }

    protected function prepareForValidation()
    {
        if (!$this->has('user_addresses')) {
            $this->merge(['user_addresses' => []]);
        }
    }

    public function rules(): array
    {
        return [
            'fullname' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->whereNull('deleted_at')
            ],
            'phone_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone_number')->whereNull('deleted_at')
            ],
            'password' => 'required|string|min:8|confirmed',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'   => ['required', Rule::in(array_keys(UserConst::STATUS))],
            'gender'   => ['nullable', Rule::in(array_keys(UserConst::GENDER))],
            'birthday' => 'nullable|date',
            'role'     => ['required', Rule::in(array_keys(UserConst::ROLE))],
            'bank_name' => 'nullable|string',
            'user_bank_name' => 'nullable|string',
            'bank_account' => 'nullable|string',

            'user_addresses' => 'required|array|min:1|max:5',
            'user_addresses.*.fullname'     => 'required_with:user_addresses|string|max:255',
            'user_addresses.*.phone_number' => 'required_with:user_addresses|string|max:20',
            'user_addresses.*.province_id'  => 'required_with:user_addresses|exists:provinces,id',
            'user_addresses.*.ward_id'      => 'required_with:user_addresses|exists:wards,id',
            'user_addresses.*.address'      => 'required_with:user_addresses|string|max:255',
            'user_addresses.*.is_default'   => 'nullable|boolean',
        ];
    }

    public function attributes()
    {
        return [
            'user_addresses.*.fullname' => 'Fullname Adress',
            'user_addresses.*.phone_number' => 'Phonenumber',
            'user_addresses.*.province_id' => 'Province',
            'user_addresses.*.ward_id' => 'Ward',
            'user_addresses.*.address' => 'Detail Address',
        ];
    }
}
