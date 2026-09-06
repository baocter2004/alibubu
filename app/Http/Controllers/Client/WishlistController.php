<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        return view('client.pages.account.wishlist', [
            'items' => Wishlist::query()
                ->where('user_id', Auth::id())
                ->with(['product.branch', 'product.variants'])
                ->latest('id')
                ->paginate(12),
        ]);
    }

    public function toggle(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)->where('is_active', true)->first();

        if (! $product) {
            $message = __('client.messages.product_not_found');

            return $request->expectsJson()
                ? response()->json(['status' => false, 'message' => $message], 404)
                : back()->with('error', $message);
        }

        $existing = Wishlist::where('user_id', Auth::id())->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();
        } else {
            Wishlist::create(['user_id' => Auth::id(), 'product_id' => $product->id]);
        }

        Auth::user()->forgetWishlistCache();

        $wishlisted = ! $existing;
        $message = __('client.wishlist.messages.' . ($wishlisted ? 'added' : 'removed'));

        if ($request->expectsJson()) {
            return response()->json([
                'status' => true,
                'wishlisted' => $wishlisted,
                'message' => $message,
                'label' => $wishlisted ? __('client.wishlist.remove') : __('client.wishlist.add'),
            ]);
        }

        return back()->with('success', $message);
    }

    public function destroy(int|string $id)
    {
        Wishlist::where('user_id', Auth::id())->whereKey($id)->delete();

        return back()->with('success', __('client.wishlist.messages.removed'));
    }
}
