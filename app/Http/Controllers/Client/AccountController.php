<?php

namespace App\Http\Controllers\Client;

use App\Const\OrderConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CancelOrderRequest;
use App\Http\Requests\Client\StoreAddressRequest;
use App\Http\Requests\Client\UpdatePasswordRequest;
use App\Http\Requests\Client\UpdateProfileRequest;
use App\Models\Province;
use App\Services\Client\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AccountController extends Controller
{
    public function __construct(protected AccountService $accountService) {}

    public function profile()
    {
        return view('client.pages.account.profile', [
            'user' => Auth::user(),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $this->accountService->updateProfile(Auth::user(), $request->validated());

        return back()->with('success', __('client.account.messages.profile_updated'));
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $this->accountService->updatePassword(Auth::user(), $request->validated()['password']);

        return back()->with('success', __('client.account.messages.password_updated'));
    }

    public function orders(Request $request)
    {
        return view('client.pages.account.orders', [
            'orders' => $this->accountService->orders(Auth::user(), $request->only(['status', 'keyword'])),
            'statuses' => OrderConst::statuses(),
        ]);
    }

    public function showOrder(int|string $id)
    {
        $order = $this->accountService->findOrder(Auth::user(), $id);

        if (! $order) {
            throw new NotFoundHttpException(__('admin/order.messages.not_found'));
        }

        return view('client.pages.account.order-detail', compact('order'));
    }

    public function cancelOrder(CancelOrderRequest $request, int|string $id)
    {
        $result = $this->accountService->cancelOrder(
            Auth::user(),
            $id,
            $request->validated()['cancel_reason'] ?? null
        );

        return redirect()
            ->route('account.orders.show', $id)
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function addresses()
    {
        return view('client.pages.account.addresses', [
            'addresses' => Auth::user()->userAddresses()->orderByDesc('is_default')->get(),
            'provinces' => Province::orderBy('name')->get(),
        ]);
    }

    public function storeAddress(StoreAddressRequest $request)
    {
        $this->accountService->storeAddress(Auth::user(), $request->validated());

        return redirect()
            ->route('account.addresses')
            ->with('success', __('client.account.messages.address_created'));
    }

    public function updateAddress(StoreAddressRequest $request, int|string $id)
    {
        if (! $this->accountService->updateAddress(Auth::user(), $id, $request->validated())) {
            return back()->with('error', __('client.account.messages.address_not_found'));
        }

        return redirect()
            ->route('account.addresses')
            ->with('success', __('client.account.messages.address_updated'));
    }

    public function destroyAddress(int|string $id)
    {
        if (! $this->accountService->deleteAddress(Auth::user(), $id)) {
            return back()->with('error', __('client.account.messages.address_not_found'));
        }

        return redirect()
            ->route('account.addresses')
            ->with('success', __('client.account.messages.address_deleted'));
    }
}
