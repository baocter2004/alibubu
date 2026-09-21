<?php

namespace App\Services\Admin;

use App\Repositories\AdminActivityLogRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminActivityLogService
{
    public static function log(string $action, $subject = null, array $properties = []): void
    {
        try {
            [$subjectType, $subjectId] = self::resolveSubject($subject);

            app(AdminActivityLogRepository::class)->newQuery()->create([
                'admin_id' => Auth::guard('admin')->id(),
                'action' => $action,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'properties' => $properties ?: null,
                'ip' => request()?->ip(),
            ]);
        } catch (\Throwable $th) {
            Log::error(__METHOD__, [
                'action' => $action,
                'message' => $th->getMessage(),
            ]);
        }
    }

    protected static function resolveSubject($subject): array
    {
        if ($subject instanceof Model) {
            return [$subject->getMorphClass(), (string) $subject->getKey()];
        }

        if (is_array($subject) && count($subject) === 2) {
            return [(string) $subject[0], (string) $subject[1]];
        }

        if (is_string($subject) && $subject !== '') {
            return [$subject, null];
        }

        return [null, null];
    }
}
