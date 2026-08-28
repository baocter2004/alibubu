<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\PlaceOrderRequest;
use App\Services\Client\CartService;
use App\Services\Client\OrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected OrderService $orderService
    ) {}

    public function index()
    {
        if ($this->cartService->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Giỏ hàng đang trống.');
        }

        $items = $this->cartService->items();

        return view('client.pages.checkout.index', [
            'items' => $items,
            'subtotal' => $this->cartService->subtotal($items),
            'defaultAddress' => Auth::user()?->userAddresses()->where('is_default', true)->first(),
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
                ->with('error', 'Đặt hàng thất bại. Vui lòng thử lại.');
        }

        return redirect()
            ->route('thanks-you')
            ->with('order_code', $order->code)
            ->with('success', 'Đặt hàng thành công!');
    }
}
