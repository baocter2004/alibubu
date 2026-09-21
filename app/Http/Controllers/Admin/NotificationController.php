<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notificationService) {}

    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $filter = $this->notificationService->filter($request->query('filter'));

        return view('admin.pages.notifications.index', [
            'notifications' => $this->notificationService->paginate($admin, $filter),
            'counts' => $this->notificationService->counts($admin),
            'unreadCount' => $this->notificationService->unreadCount($admin),
            'filter' => $filter,
        ]);
    }

    public function read(string $id): RedirectResponse
    {
        $notification = $this->notificationService->markAsRead(Auth::guard('admin')->user(), $id);

        if (! $notification) {
            return back()->with('error', __('admin/notification.messages.not_found'));
        }

        $target = $this->notificationService->targetPath($notification);

        return $target
            ? redirect()->to($target)
            : back()->with('success', __('admin/notification.messages.marked_read'));
    }

    public function readAll(): RedirectResponse
    {
        $this->notificationService->markAllAsRead(Auth::guard('admin')->user());

        return back()->with('success', __('admin/notification.messages.marked_all_read'));
    }

    public function destroyRead(): RedirectResponse
    {
        $deleted = $this->notificationService->deleteRead(Auth::guard('admin')->user());

        return back()->with('success', __('admin/notification.messages.deleted_read', ['count' => $deleted]));
    }
}
