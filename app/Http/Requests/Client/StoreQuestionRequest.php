<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'min:10', 'max:500'],
            'fullname' => [Rule::requiredIf(! $this->user()), 'nullable', 'string', 'max:120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'question' => __('client.question.fields.question'),
            'fullname' => __('client.question.fields.fullname'),
        ];
    }
}
