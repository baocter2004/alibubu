<?php

namespace App\Services\Admin;

use App\Const\QuestionConst;
use App\Models\Admin;
use App\Models\ProductQuestion;
use App\Notifications\QuestionAnswered;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class QuestionService
{
    public function paginate(?string $status): LengthAwarePaginator
    {
        return ProductQuestion::query()
            ->with(['product', 'user', 'admin'])
            ->when($status === QuestionConst::FILTER_PENDING, fn ($query) => $query->whereNull('answer'))
            ->when($status === QuestionConst::FILTER_ANSWERED, fn ($query) => $query->whereNotNull('answer'))
            ->when($status === QuestionConst::FILTER_HIDDEN, fn ($query) => $query->where('is_published', false))
            ->latest('id')
            ->paginate(QuestionConst::ADMIN_PER_PAGE);
    }

    public function pendingCount(): int
    {
        return ProductQuestion::query()->whereNull('answer')->count();
    }

    public function answer(string $id, string $answer, Admin $admin): array
    {
        $question = ProductQuestion::query()->with(['product', 'user'])->find($id);

        if (! $question) {
            return ['status' => false, 'message' => __('admin/question.messages.not_found')];
        }

        $firstAnswer = ! $question->isAnswered();

        $question->update([
            'answer' => $answer,
            'answered_by' => $admin->getKey(),
            'answered_at' => now(),
            'is_published' => true,
        ]);

        if ($firstAnswer) {
            $this->notifyAsker($question);
        }

        return ['status' => true, 'message' => __('admin/question.messages.answered')];
    }

    public function toggle(string $id): array
    {
        $question = ProductQuestion::query()->find($id);

        if (! $question) {
            return ['status' => false, 'message' => __('admin/question.messages.not_found')];
        }

        $question->update(['is_published' => ! $question->is_published]);

        return ['status' => true, 'message' => __('admin/question.messages.visibility_updated')];
    }

    public function delete(string $id): array
    {
        ProductQuestion::query()->whereKey($id)->delete();

        return ['status' => true, 'message' => __('admin/question.messages.deleted')];
    }

    protected function notifyAsker(ProductQuestion $question): void
    {
        try {
            if ($question->user) {
                $question->user->notify(new QuestionAnswered($question));

                return;
            }

            if (filled($question->email)) {
                Notification::route('mail', $question->email)->notify(new QuestionAnswered($question));
            }
        } catch (Throwable $e) {
            Log::error(__METHOD__, ['message' => $e->getMessage(), 'question_id' => $question->id]);
        }
    }
}
