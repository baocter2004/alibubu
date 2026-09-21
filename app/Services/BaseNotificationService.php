<?php

namespace App\Services;

use App\Const\NotificationConst;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;

abstract class BaseNotificationService
{
    abstract protected function langPrefix(): string;

    abstract protected function perPage(): int;

    abstract protected function legacyUrl(string $type, array $data): ?string;

    protected function legacyTypes(): array
    {
        return [];
    }

    protected function legacyIcons(): array
    {
        return [];
    }

    protected function legacyParams(string $type, array $data): array
    {
        return array_filter($data, 'is_scalar');
    }

    public function filter(?string $filter): string
    {
        return $filter === NotificationConst::FILTER_UNREAD ? NotificationConst::FILTER_UNREAD : NotificationConst::FILTER_ALL;
    }

    public function paginate(Model $notifiable, string $filter = NotificationConst::FILTER_ALL): LengthAwarePaginator
    {
        $query = $filter === NotificationConst::FILTER_UNREAD
            ? $notifiable->unreadNotifications()
            : $notifiable->notifications();

        return $query->paginate($this->perPage())
            ->through(fn (DatabaseNotification $notification) => $this->present($notification));
    }

    public function latest(Model $notifiable, int $limit = NotificationConst::LATEST_LIMIT): Collection
    {
        return $notifiable->notifications()
            ->limit($limit)
            ->get()
            ->map(fn (DatabaseNotification $notification) => $this->present($notification));
    }

    public function unreadCount(Model $notifiable): int
    {
        $key = $this->cacheKey($notifiable);
        $attributes = request()->attributes;

        if (! $attributes->has($key)) {
            $attributes->set($key, $notifiable->unreadNotifications()->count());
        }

        return (int) $attributes->get($key);
    }

    public function counts(Model $notifiable): array
    {
        $total = $notifiable->notifications()->count();
        $unread = $this->unreadCount($notifiable);

        return [
            NotificationConst::FILTER_ALL => $total,
            NotificationConst::FILTER_UNREAD => $unread,
            'read' => max(0, $total - $unread),
        ];
    }

    public function find(Model $notifiable, string $id): ?DatabaseNotification
    {
        return $notifiable->notifications()->whereKey($id)->first();
    }

    public function markAsRead(Model $notifiable, string $id): ?DatabaseNotification
    {
        $notification = $this->find($notifiable, $id);

        if ($notification) {
            $notification->markAsRead();
            $this->forgetUnreadCount($notifiable);
        }

        return $notification;
    }

    public function markAllAsRead(Model $notifiable): int
    {
        $updated = $notifiable->unreadNotifications()->update(['read_at' => now()]);
        $this->forgetUnreadCount($notifiable);

        return $updated;
    }

    public function deleteRead(Model $notifiable): int
    {
        return $notifiable->readNotifications()->delete();
    }

    public function present(DatabaseNotification $notification): array
    {
        $data = is_array($notification->data) ? $notification->data : [];
        $type = $this->typeOf($data);
        $params = $this->params($type, $data);
        $key = $this->langPrefix() . '.' . $type;
        $known = $type !== '' && Lang::has($key . '.title');
        $icon = (string) ($data['icon'] ?? ($this->legacyIcons()[$type] ?? NotificationConst::DEFAULT_ICON));
        $level = $data['level'] ?? NotificationConst::LEVEL_INFO;

        return [
            'id' => $notification->id,
            'type' => $type,
            'title' => $known ? __($key . '.title', $params) : __($this->langPrefix() . '.default.title'),
            'body' => $known && Lang::has($key . '.body') ? __($key . '.body', $params) : '',
            'reason' => filled($params['reason'] ?? null) ? $params['reason'] : null,
            'icon' => preg_match('/^fa-[a-z0-9-]+$/', $icon) ? $icon : NotificationConst::DEFAULT_ICON,
            'level' => in_array($level, NotificationConst::levels(), true) ? $level : NotificationConst::LEVEL_INFO,
            'url' => $this->targetPath($notification),
            'is_read' => $notification->read_at !== null,
            'time' => $notification->created_at?->diffForHumans(),
            'datetime' => $notification->created_at?->format('d/m/Y H:i'),
        ];
    }

    public function targetPath(DatabaseNotification $notification): ?string
    {
        $data = is_array($notification->data) ? $notification->data : [];
        $url = $data['url'] ?? null;

        if (is_string($url) && ($path = $this->relativePath($url)) !== null) {
            return $path;
        }

        $legacy = $this->legacyUrl($this->typeOf($data), $data);

        return $legacy ? $this->relativePath($legacy) : null;
    }

    protected function typeOf(array $data): string
    {
        $type = is_string($data['type'] ?? null) ? $data['type'] : '';

        return $this->legacyTypes()[$type] ?? $type;
    }

    protected function params(string $type, array $data): array
    {
        $params = is_array($data['params'] ?? null)
            ? $data['params']
            : $this->legacyParams($type, $data);

        return collect($params)
            ->filter(fn ($value, $key) => is_string($key) && (is_scalar($value) || $value === null))
            ->map(fn ($value) => (string) $value)
            ->all();
    }

    protected function relativePath(string $url): ?string
    {
        $parts = parse_url($url);

        if ($parts === false) {
            return null;
        }

        $path = $parts['path'] ?? '/';

        if (! str_starts_with($path, '/') || str_starts_with($path, '//')) {
            return null;
        }

        return $path
            . (isset($parts['query']) ? '?' . $parts['query'] : '')
            . (isset($parts['fragment']) ? '#' . $parts['fragment'] : '');
    }

    protected function forgetUnreadCount(Model $notifiable): void
    {
        request()->attributes->remove($this->cacheKey($notifiable));
    }

    protected function cacheKey(Model $notifiable): string
    {
        return 'notifications.unread.' . $notifiable::class . '.' . $notifiable->getKey();
    }
}
