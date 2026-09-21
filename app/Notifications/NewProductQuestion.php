<?php

namespace App\Notifications;

use App\Const\NotificationConst;
use App\Const\QuestionConst;
use App\Models\ProductQuestion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewProductQuestion extends Notification implements ShouldQueue
{
    use Queueable;

    public $afterCommit = true;

    public function __construct(public ProductQuestion $question) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function viaConnections(): array
    {
        return ['database' => 'sync'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'question.asked',
            'url' => route('admin.questions.index', ['status' => QuestionConst::FILTER_PENDING]) . '#question-' . $this->question->id,
            'icon' => 'fa-comments',
            'level' => NotificationConst::LEVEL_INFO,
            'params' => [
                'product' => $this->question->product?->name ?? '-',
                'customer' => $this->question->askerName() ?: '-',
                'question' => Str::limit($this->question->question, QuestionConst::EXCERPT_LENGTH),
            ],
            'question_id' => $this->question->id,
            'product_id' => $this->question->product_id,
        ];
    }
}
