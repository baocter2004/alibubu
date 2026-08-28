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
            return back()->with('error', 'Sản phẩm không còn khả dụng.');
        }

        $variant = null;

        if (! empty($data['product_variant_id'])) {
            $variant = $product->variants->firstWhere('id', $data['product_variant_id']);

            if (! $variant) {
                return back()->with('error', 'Phiên bản sản phẩm không hợp lệ.');
            }
        } elseif ($product->hasVariants()) {
            return back()->with('error', 'Vui lòng chọn phiên bản sản phẩm.');
        }

        $this->cartService->add($product, $variant, (int) ($data['quantity'] ?? 1));

        return redirect()
            ->route('cart.index')
            ->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
    }

    public function update(UpdateCartItemRequest $request, string $key)
    {
        $this->cartService->update($key, (int) $request->validated()['quantity']);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Đã cập nhật giỏ hàng.');
    }

    public function destroy(string $key)
    {
        $this->cartService->remove($key);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Đã xoá sản phẩm khỏi giỏ hàng.');
    }

    public function clear()
    {
        $this->cartService->clear();

        return redirect()
            ->route('cart.index')
            ->with('success', 'Đã xoá toàn bộ giỏ hàng.');
    }
}
