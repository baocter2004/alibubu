<?php

namespace App\Http\Controllers\Client;

use App\Const\OrderConst;
use App\Const\PaymentConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\PlaceOrderRequest;
use App\Services\Client\CartService;
use App\Services\Client\CouponService;
use App\Services\Client\OrderService;
use App\Services\Order\OrderStateService;
use App\Services\Payment\MomoService;
use App\Services\Payment\VnpayService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CouponService $couponService,
        protected OrderService $orderService,
        protected VnpayService $vnpayService,
        protected MomoService $momoService,
        protected OrderStateService $orderState
    ) {}

    public function index()
    {
        if ($this->cartService->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', __('client.messages.cart_empty'));
        }

        $items = $this->cartService->items();
        $subtotal = $this->cartService->subtotal($items);
        $applied = $this->couponService->current($items, $subtotal, Auth::user());
        $tier = Auth::user()?->membershipTier();
        $membershipDiscount = $tier ? \App\Const\MembershipConst::discountFor($tier, $subtotal) : 0.0;

        $addresses = Auth::check()
            ? Auth::user()->userAddresses()->orderByDesc('is_default')->latest('id')->get()
            : collect();

        return view('client.pages.checkout.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'coupon' => $applied['coupon'] ?? null,
            'discount' => $applied['discount'] ?? 0.0,
            'membershipTier' => $tier,
            'membershipDiscount' => $membershipDiscount,
            'total' => max($subtotal - ($applied['discount'] ?? 0.0) - $membershipDiscount, 0),
            'addresses' => $addresses,
            'defaultAddress' => $addresses->firstWhere('is_default', true) ?? $addresses->first(),
            'vnpayEnabled' => $this->vnpayService->isEnabled(),
            'momoEnabled' => $this->momoService->isEnabled(),
            'bankTransferEnabled' => (bool) config('payment.bank_transfer.enabled'),
        ]);
    }

    public function store(PlaceOrderRequest $request)
    {
        try {
            $order = $this->orderService->place($request->validated(), Auth::id());
        } catch (\RuntimeException $th) {
            return redirect()
                ->route('cart.index')
                ->with('error', $th->getMessage());
        } catch (\Throwable $th) {
            Log::error(__METHOD__, [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('client.messages.order_failed'));
        }

        $gateway = $order->canPayOnline() ? match ((int) $order->payment_method) {
            PaymentConst::METHOD_VNPAY => $this->vnpayService->isEnabled() ? 'vnpay' : null,
            PaymentConst::METHOD_MOMO => $this->momoService->isEnabled() ? 'momo' : null,
            default => null,
        } : null;

        if ($gateway) {
            try {
                return redirect()->away($gateway === 'vnpay'
                    ? $this->vnpayService->createPaymentUrl($order, $request->ip())
                    : $this->momoService->createPaymentUrl($order));
            } catch (\Throwable $th) {
                Log::error(__METHOD__, ['message' => $th->getMessage(), 'order_code' => $order->code]);

                return redirect()
                    ->route('order.track', ['code' => $order->code])
                    ->with('error', __('client.payment.messages.gateway_unavailable'));
            }
        }

        return redirect()
            ->route('thanks-you')
            ->with('order_code', $order->code)
            ->with('order_id', $order->id)
            ->with('success', __('client.messages.order_success'));
    }
}
