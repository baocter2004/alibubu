<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreCartItemRequest;
use App\Http\Requests\Client\UpdateCartItemRequest;
use App\Models\Product;
use App\Services\Client\CartService;
use App\Services\Client\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CouponService $couponService
    ) {}

    public function index()
    {
        return view('client.pages.cart.index', $this->summary());
    }

    public function store(StoreCartItemRequest $request)
    {
        $data = $request->validated();

        $product = Product::query()
            ->with('variants')
            ->where('is_active', true)
            ->find($data['product_id']);

        if (! $product) {
            return $this->failed($request, __('client.messages.product_unavailable'));
        }

        $variant = null;

        if (! empty($data['product_variant_id'])) {
            $variant = $product->variants->firstWhere('id', $data['product_variant_id']);

            if (! $variant) {
                return $this->failed($request, __('client.messages.variant_invalid'));
            }
        } elseif ($product->hasVariants()) {
            return $this->failed($request, __('client.messages.variant_required'));
        }

        if (! $product->inStock()) {
            return $this->failed($request, __('client.messages.out_of_stock', ['name' => $product->name]));
        }

        $this->cartService->add($product, $variant, (int) ($data['quantity'] ?? 1));

        $message = __('client.messages.cart_added');
        $buyNow = $request->boolean('buy_now');

        if ($request->expectsJson()) {
            return $this->json($message, ['redirect' => $buyNow ? route('checkout.index') : null]);
        }

        return redirect()
            ->to($buyNow ? route('checkout.index') : route('cart.index'))
            ->with('success', $message);
    }

    public function update(UpdateCartItemRequest $request, string $key)
    {
        $this->cartService->update($key, (int) $request->validated()['quantity']);

        return $this->done($request, __('client.messages.cart_updated'));
    }

    public function destroy(Request $request, string $key)
    {
        $this->cartService->remove($key);

        return $this->done($request, __('client.messages.cart_removed'));
    }

    public function clear(Request $request)
    {
        $this->cartService->clear();

        return $this->done($request, __('client.messages.cart_cleared'));
    }

    protected function summary(): array
    {
        $items = $this->cartService->items();
        $subtotal = $this->cartService->subtotal($items);
        $applied = $this->couponService->current($items, $subtotal, Auth::user());

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'coupon' => $applied['coupon'] ?? null,
            'discount' => $applied['discount'] ?? 0.0,
            'total' => $subtotal - ($applied['discount'] ?? 0.0),
        ];
    }

    protected function done(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return $this->json($message);
        }

        return redirect()->route('cart.index')->with('success', $message);
    }

    protected function failed(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['status' => false, 'message' => $message], 422);
        }

        return back()->with('error', $message);
    }

    protected function json(string $message, array $extra = []): JsonResponse
    {
        $summary = $this->summary();

        return response()->json(array_merge([
            'status' => true,
            'message' => $message,
            'count' => $this->cartService->count(),
            'lines' => $summary['items']
                ->mapWithKeys(fn (array $item) => [$item['key'] => [
                    'quantity' => $item['quantity'],
                    'subtotal' => format_price($item['subtotal']),
                ]])
                ->all(),
            'totals' => [
                'subtotal' => format_price($summary['subtotal']),
                'discount' => format_price($summary['discount']),
                'total' => format_price($summary['total']),
                'has_discount' => $summary['discount'] > 0,
                'is_empty' => $summary['items']->isEmpty(),
            ],
        ], $extra));
    }
}
