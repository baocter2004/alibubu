<?php

namespace App\Services\Admin;

use App\Const\NotificationConst;
use App\Const\QuestionConst;
use App\Const\ReviewConst;
use App\Services\BaseNotificationService;
use Illuminate\Support\Str;

class NotificationService extends BaseNotificationService
{
    protected function langPrefix(): string
    {
        return 'admin/notification.types';
    }

    protected function perPage(): int
    {
        return NotificationConst::ADMIN_PER_PAGE;
    }

    protected function legacyTypes(): array
    {
        return [
            'product.question' => 'question.asked',
        ];
    }

    protected function legacyIcons(): array
    {
        return [
            'order.placed' => 'fa-receipt',
            'question.asked' => 'fa-comments',
            'review.submitted' => 'fa-star',
        ];
    }

    protected function legacyParams(string $type, array $data): array
    {
        return array_merge(array_filter($data, 'is_scalar'), array_filter([
            'code' => $data['order_code'] ?? null,
            'product' => $data['product_name'] ?? null,
            'customer' => $data['customer'] ?? $data['asked_by'] ?? null,
            'items' => $data['items_count'] ?? null,
            'total' => isset($data['total_amount']) ? format_price($data['total_amount']) : null,
            'question' => isset($data['question']) ? Str::limit((string) $data['question'], QuestionConst::EXCERPT_LENGTH) : null,
        ], fn ($value) => $value !== null));
    }

    protected function legacyUrl(string $type, array $data): ?string
    {
        if (! empty($data['order_id'])) {
            return route('admin.orders.show', $data['order_id']);
        }

        if (! empty($data['question_id'])) {
            return route('admin.questions.index', ['status' => QuestionConst::FILTER_PENDING]) . '#question-' . $data['question_id'];
        }

        if (! empty($data['review_id'])) {
            return route('admin.reviews.index', ['status' => ReviewConst::STATUS_PENDING]) . '#review-' . $data['review_id'];
        }

        return null;
    }
}
