<?php

namespace App\Services\Admin;

use App\Const\AdminActivityConst;
use App\Repositories\AdminActivityLogRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

    public function paginate(array $filters = [], int $perPage = AdminActivityConst::PER_PAGE): LengthAwarePaginator
    {
        return app(AdminActivityLogRepository::class)->newQuery()
            ->with('admin')
            ->when($filters['action'] ?? null, fn ($query, $action) => $query->where('action', $action))
            ->when($filters['admin_id'] ?? null, fn ($query, $adminId) => $query->where('admin_id', $adminId))
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function actions(): array
    {
        return app(AdminActivityLogRepository::class)->newQuery()
            ->distinct()
            ->orderBy('action')
            ->pluck('action')
            ->all();
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
