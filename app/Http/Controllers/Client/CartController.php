<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreCartItemRequest;
use App\Http\Requests\Client\UpdateCartItemRequest;
use App\Models\Product;
use App\Services\Client\CartService;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function index()
    {
        $items = $this->cartService->items();

        return view('client.pages.cart.index', [
            'items' => $items,
            'subtotal' => $this->cartService->subtotal($items),
        ]);
    }

    public function store(StoreCartItemRequest $request)
    {
        $data = $request->validated();

        $product = Product::query()
            ->with('variants')
            ->where('is_active', true)
            ->find($data['product_id']);

        if (! $product) {
            return back()->with('error', __('client.messages.product_unavailable'));
        }

        $variant = null;

        if (! empty($data['product_variant_id'])) {
            $variant = $product->variants->firstWhere('id', $data['product_variant_id']);

            if (! $variant) {
                return back()->with('error', __('client.messages.variant_invalid'));
            }
        } elseif ($product->hasVariants()) {
            return back()->with('error', __('client.messages.variant_required'));
        }

        $this->cartService->add($product, $variant, (int) ($data['quantity'] ?? 1));

        return redirect()
            ->route('cart.index')
            ->with('success', __('client.messages.cart_added'));
    }

    public function update(UpdateCartItemRequest $request, string $key)
    {
        $this->cartService->update($key, (int) $request->validated()['quantity']);

        return redirect()
            ->route('cart.index')
            ->with('success', __('client.messages.cart_updated'));
    }

    public function destroy(string $key)
    {
        $this->cartService->remove($key);

        return redirect()
            ->route('cart.index')
            ->with('success', __('client.messages.cart_removed'));
    }

    public function clear()
    {
        $this->cartService->clear();

        return redirect()
            ->route('cart.index')
            ->with('success', __('client.messages.cart_cleared'));
    }
}
