<?php

namespace App\Http\Requests\Admin\Users;

use App\Const\UserConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $addresses = collect($this->input('user_addresses', []));

            $activeCount = $addresses->where('is_active', true)->count();

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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
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

            'avatar' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            'phone_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone_number')->whereNull('deleted_at')
            ],

            'password' => 'required|string|min:8|confirmed',

            'status' => ['nullable', Rule::in(array_keys(UserConst::STATUS))],
            'gender' => ['nullable', Rule::in(array_keys(UserConst::GENDER))],
            'birthday' => 'nullable|date',
            'role' => ['required', Rule::in(array_keys(UserConst::ROLE))],

            'user_addresses' => 'nullable|array|max:5',

            'user_addresses.*.address' => 'required_with:user_addresses|string|max:255',

            'user_addresses.*.province_id' => [
                'required_with:user_addresses',
                'integer',
                Rule::exists('provinces', 'id')
            ],

            'user_addresses.*.ward_id' => [
                'required_with:user_addresses',
                'integer',
                Rule::exists('wards', 'id')
            ],

            'user_addresses.*.is_active' => 'nullable|boolean',
        ];
    }
}
