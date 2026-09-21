<?php

namespace App\Services\Client;

use App\Const\PermissionConst;
use App\Const\QuestionConst;
use App\Models\Product;
use App\Models\ProductQuestion;
use App\Models\User;
use App\Notifications\NewProductQuestion;
use App\Services\Admin\AdminNotifierService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class QuestionService
{
    public function ownPending(Product $product): Collection
    {
        $ids = session()->get(QuestionConst::SESSION_KEY, []);

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
        session()->put(QuestionConst::SESSION_KEY, array_slice(
            array_merge([(string) $question->id], session()->get(QuestionConst::SESSION_KEY, [])),
            0,
            QuestionConst::SESSION_LIMIT
        ));
    }

    protected function notifyAdmins(ProductQuestion $question): void
    {
        try {
            AdminNotifierService::notify(new NewProductQuestion($question->loadMissing(['product', 'user'])), PermissionConst::QUESTIONS_ANSWER);
        } catch (\Throwable $th) {
            Log::error(__METHOD__, ['message' => $th->getMessage(), 'question_id' => $question->id]);
        }
    }

    protected function askedRecently(Product $product, ?User $user, ?string $ip): bool
    {
        $query = ProductQuestion::query()
            ->where('product_id', $product->id)
            ->where('created_at', '>=', now()->subMinutes(QuestionConst::COOLDOWN_MINUTES));

        if ($user) {
            return $query->where('user_id', $user->id)->exists();
        }

        $sessionIds = session()->get(QuestionConst::SESSION_KEY, []);

        if ($sessionIds === [] && blank($ip)) {
            return false;
        }

        return $query
            ->whereNull('user_id')
            ->where(function ($sub) use ($sessionIds, $ip) {
                $sub->whereIn('id', $sessionIds);

                if (filled($ip)) {
                    $sub->orWhere('ip_address', $ip);
                }
            })
            ->exists();
    }

    public function paginateFor(Product $product, int $limit = QuestionConst::CLIENT_PER_PAGE): LengthAwarePaginator
    {
        return $product->publishedQuestions()
            ->with(['user', 'admin'])
            ->paginate($limit, ['*'], 'questions');
    }

    public function ask(Product $product, ?User $user, array $params): array
    {
        $ip = request()->ip();

        if ($this->askedRecently($product, $user, $ip)) {
            return [
                'status' => false,
                'message' => __('client.question.messages.too_fast'),
            ];
        }

        $question = ProductQuestion::create([
            'product_id' => $product->id,
            'user_id' => $user?->id,
            'fullname' => $user?->fullname ?: ($params['fullname'] ?? null),
            'email' => $user ? null : ($params['email'] ?? null),
            'ip_address' => $ip,
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
