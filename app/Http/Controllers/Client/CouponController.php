<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ApplyCouponRequest;
use App\Services\Client\CartService;
use App\Services\Client\CouponService;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CouponService $couponService
    ) {}

    public function store(ApplyCouponRequest $request)
    {
        $items = $this->cartService->items();

        if ($items->isEmpty()) {
            return back()->with('error', __('client.messages.cart_empty'));
        }

        $result = $this->couponService->apply(
            $request->validated()['code'],
            $items,
            $this->cartService->subtotal($items),
            Auth::user()
        );

        return back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function destroy()
    {
        $this->couponService->forget();

        return back()->with('success', __('client.coupon.messages.removed'));
    }
}
