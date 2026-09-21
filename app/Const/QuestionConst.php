<?php

namespace App\Const;

class QuestionConst
{
    const ADMIN_PER_PAGE = 15;
    const CLIENT_PER_PAGE = 5;

    const SESSION_KEY = 'asked_questions';
    const SESSION_LIMIT = 20;
    const COOLDOWN_MINUTES = 2;
    const EXCERPT_LENGTH = 120;

    const FILTER_PENDING = 'pending';
    const FILTER_ANSWERED = 'answered';
    const FILTER_HIDDEN = 'hidden';

    public static function filters(): array
    {
        return [
            '' => __('admin/question.filters.all'),
            self::FILTER_PENDING => __('admin/question.filters.pending'),
            self::FILTER_ANSWERED => __('admin/question.filters.answered'),
            self::FILTER_HIDDEN => __('admin/question.filters.hidden'),
        ];
    }
}
