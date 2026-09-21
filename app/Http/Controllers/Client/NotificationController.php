<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Client\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notificationService) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $this->notificationService->filter($request->query('filter'));

        return view('client.pages.account.notifications', [
            'notifications' => $this->notificationService->paginate($user, $filter),
            'counts' => $this->notificationService->counts($user),
            'filter' => $filter,
        ]);
    }

    public function read(string $id): RedirectResponse
    {
        $notification = $this->notificationService->markAsRead(Auth::user(), $id);

        if (! $notification) {
            return redirect()->route('account.notifications.index')
                ->with('error', __('client.notifications.messages.not_found'));
        }

        $target = $this->notificationService->targetPath($notification);

        return $target
            ? redirect()->to($target)
            : redirect()->route('account.notifications.index')
                ->with('success', __('client.notifications.messages.marked_read'));
    }

    public function readAll(): RedirectResponse
    {
        $this->notificationService->markAllAsRead(Auth::user());

        return back()->with('success', __('client.notifications.messages.marked_all_read'));
    }
}
