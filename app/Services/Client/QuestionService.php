<?php

namespace App\Services\Client;

use App\Models\Admin;
use App\Models\Product;
use App\Models\ProductQuestion;
use App\Models\User;
use App\Notifications\NewProductQuestion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class QuestionService
{
    public const PER_PAGE = 5;

    public const SESSION_KEY = 'asked_questions';

    public function ownPending(Product $product): Collection
    {
        $ids = session()->get(self::SESSION_KEY, []);

        if ($ids === []) {
            return new Collection();
        }

        return ProductQuestion::query()
            ->whereIn('id', $ids)
            ->where('product_id', $product->id)
            ->where('is_published', false)
            ->latest('id')
            ->get();
    }

    protected function remember(ProductQuestion $question): void
    {
        session()->put(self::SESSION_KEY, array_slice(
            array_merge([(string) $question->id], session()->get(self::SESSION_KEY, [])),
            0,
            20
        ));
    }

    protected function notifyAdmins(ProductQuestion $question): void
    {
        try {
            $admins = Admin::query()->get();

            if ($admins->isEmpty()) {
                return;
            }

            Notification::send($admins, new NewProductQuestion($question->loadMissing('product')));
        } catch (\Throwable $th) {
            Log::error(__METHOD__, ['message' => $th->getMessage(), 'question_id' => $question->id]);
        }
    }

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

        $question = ProductQuestion::create([
            'product_id' => $product->id,
            'user_id' => $user?->id,
            'fullname' => $user?->fullname ?: ($params['fullname'] ?? null),
            'question' => $params['question'],
            'is_published' => false,
        ]);

        $this->remember($question);
        $this->notifyAdmins($question);

        return [
            'status' => true,
            'message' => __('client.question.messages.received'),
        ];
    }
}
