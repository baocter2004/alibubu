<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public const PER_PAGE = 20;

    public function index()
    {
        $admin = Auth::guard('admin')->user();

        return view('admin.pages.notifications.index', [
            'notifications' => $admin->notifications()->paginate(self::PER_PAGE),
            'unreadCount' => $admin->unreadNotifications()->count(),
        ]);
    }

    public function read(string $id): RedirectResponse
    {
        $notification = Auth::guard('admin')->user()->notifications()->whereKey($id)->first();

        if (! $notification) {
            return back()->with('error', __('common.empty.title'));
        }

        $notification->markAsRead();

        $target = $notification->data['order_id'] ?? null;

        return $target
            ? redirect()->route('admin.orders.show', $target)
            : back()->with('success', __('admin/notification.messages.marked_read'));
    }

    public function readAll(): RedirectResponse
    {
        Auth::guard('admin')->user()->unreadNotifications->markAsRead();

        return back()->with('success', __('admin/notification.messages.marked_all_read'));
    }
}
