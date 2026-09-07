<?php

namespace App\Notifications;

use App\Models\ProductQuestion;
use Illuminate\Notifications\Notification;

class NewProductQuestion extends Notification
{
    public function __construct(public ProductQuestion $question) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'product.question',
            'question_id' => $this->question->id,
            'product_id' => $this->question->product_id,
            'product_name' => $this->question->product?->name,
            'asked_by' => $this->question->user?->fullname ?: $this->question->fullname,
            'question' => $this->question->question,
        ];
    }
}
