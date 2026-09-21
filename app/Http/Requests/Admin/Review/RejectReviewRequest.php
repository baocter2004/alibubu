<?php

namespace App\Http\Requests\Admin\Review;

use App\Const\ReviewConst;
use Illuminate\Foundation\Http\FormRequest;

class RejectReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string', 'max:' . ReviewConst::REASON_MAX_LENGTH],
        ];
    }

    public function attributes(): array
    {
        return [
            'reason' => __('admin/review.fields.reason'),
        ];
    }
}
