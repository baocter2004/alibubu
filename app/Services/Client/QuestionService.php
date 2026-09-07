<?php

namespace App\Services\Client;

use App\Models\Product;
use App\Models\ProductQuestion;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class QuestionService
{
    public const PER_PAGE = 5;

    public function paginateFor(Product $product, int $limit = self::PER_PAGE): LengthAwarePaginator
    {
        return $product->publishedQuestions()
            ->with(['user', 'admin'])
            ->paginate($limit, ['*'], 'questions');
    }

    public function ask(Product $product, ?User $user, array $params): array
    {
        $recent = ProductQuestion::query()
            ->where('product_id', $product->id)
            ->when($user, fn ($query) => $query->where('user_id', $user->id))
            ->where('created_at', '>=', now()->subMinutes(2))
            ->exists();

        if ($recent) {
            return [
                'status' => false,
                'message' => __('client.question.messages.too_fast'),
            ];
        }

        ProductQuestion::create([
            'product_id' => $product->id,
            'user_id' => $user?->id,
            'fullname' => $user?->fullname ?: ($params['fullname'] ?? null),
            'question' => $params['question'],
            'is_published' => false,
        ]);

        return [
            'status' => true,
            'message' => __('client.question.messages.received'),
        ];
    }
}
