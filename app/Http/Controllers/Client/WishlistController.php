<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
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

    public function toggle(string $slug)
    {
        $product = Product::where('slug', $slug)->where('is_active', true)->first();

        if (! $product) {
            return back()->with('error', __('client.messages.product_not_found'));
        }

        $existing = Wishlist::where('user_id', Auth::id())->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();

            return back()->with('success', __('client.wishlist.messages.removed'));
        }

        Wishlist::create(['user_id' => Auth::id(), 'product_id' => $product->id]);

        return back()->with('success', __('client.wishlist.messages.added'));
    }

    public function destroy(int|string $id)
    {
        Wishlist::where('user_id', Auth::id())->whereKey($id)->delete();

        return back()->with('success', __('client.wishlist.messages.removed'));
    }
}
