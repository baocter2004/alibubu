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

    protected function prepareForValidation()
    {
        $this->merge([
            'user_addresses' => $this->input('user_addresses', [])
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $addresses = collect($this->input('user_addresses', []));

            $validAddressCount = 0;

            foreach ($addresses as $index => $address) {

                $hasAnyValue = collect($address)
                    ->except('is_default')
                    ->filter(fn($v) => !is_null($v) && $v !== '')
                    ->isNotEmpty();

                if ($hasAnyValue) {
                    foreach (['fullname', 'phone_number', 'province_id', 'ward_id', 'address'] as $field) {
                        if (empty($address[$field])) {
                            $validator->errors()->add(
                                "user_addresses.$index.$field",
                                "Trường {$this->attributes()["user_addresses.*.$field"]} không được bỏ trống"
                            );
                        }
                    }

                    if (
                        !empty($address['fullname']) &&
                        !empty($address['phone_number']) &&
                        !empty($address['province_id']) &&
                        !empty($address['ward_id']) &&
                        !empty($address['address'])
                    ) {
                        $validAddressCount++;
                    }
                }
            }

            if ($validAddressCount === 0) {
                $validator->errors()->add(
                    'user_addresses',
                    'Phải có ít nhất 1 địa chỉ hợp lệ'
                );
            }

            $activeCount = $addresses->where('is_default', true)->count();

            if ($validAddressCount > 0) {
                if ($activeCount > 1) {
                    $validator->errors()->add(
                        'user_addresses',
                        'Chỉ được có 1 địa chỉ mặc định'
                    );
                }

                if ($activeCount === 0) {
                    $validator->errors()->add(
                        'user_addresses',
                        'Phải có 1 địa chỉ mặc định'
                    );
                }
            }
        });
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'fullname' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id)
            ],
            'phone_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone_number')->ignore($id)
            ],
            'password' => 'nullable|min:8|confirmed',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'   => ['required', Rule::in(array_keys(UserConst::STATUS))],
            'gender'   => ['nullable', Rule::in(array_keys(UserConst::GENDER))],
            'birthday' => 'nullable|date',
            'role'     => ['required', Rule::in(array_keys(UserConst::ROLE))],

            'bank_name' => [
                'nullable',
                Rule::in(array_keys(\App\Const\BankConst::getOptions()))
            ],
            'user_bank_name' => 'nullable|string|regex:/^[A-Z\s]+$/',
            'bank_account' => 'nullable|string',

            'user_addresses' => 'array|max:5',

            'user_addresses.*.fullname'     => 'nullable|string|max:255',
            'user_addresses.*.phone_number' => 'nullable|string|max:20',
            'user_addresses.*.province_id'  => 'nullable|exists:provinces,id',
            'user_addresses.*.ward_id'      => 'nullable|exists:wards,id',
            'user_addresses.*.address'      => 'nullable|string|max:255',
            'user_addresses.*.is_default'   => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'user_bank_name.regex' => 'Tên chủ tài khoản chỉ được chứa chữ in hoa và khoảng trắng',
        ];
    }

    public function attributes()
    {
        return [
            'fullname' => 'họ và tên',
            'email' => 'Email',
            'phone_number' => 'số điện thoại',
            'password' => 'mật khẩu',
            'avatar' => 'ảnh đại diện',
            'status' => 'trạng thái',
            'gender' => 'giới tính',
            'birthday' => 'ngày sinh',
            'role' => 'vai trò',

            'bank_name' => 'tên ngân hàng',
            'user_bank_name' => 'tên chủ tài khoản',
            'bank_account' => 'số tài khoản',

            'user_addresses' => 'địa chỉ người dùng',
            'user_addresses.*.fullname' => 'tên người nhận',
            'user_addresses.*.phone_number' => 'số điện thoại',
            'user_addresses.*.province_id' => 'tỉnh/thành phố',
            'user_addresses.*.ward_id' => 'phường/xã',
            'user_addresses.*.address' => 'địa chỉ chi tiết',
        ];
    }
}
