<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use App\Services\Client\ProductService;

class HomeController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function index()
    {
        return view('client.pages.index', [
            'categories' => Category::query()
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('ordinal')
                ->withCount('products')
                ->get(),
            'brands' => Branch::query()
                ->where('is_active', true)
                ->has('products')
                ->withCount('products')
                ->orderByDesc('products_count')
                ->limit(10)
                ->get(),
            'featuredProducts' => $this->productService->highlights('is_featured', 8),
            'trendingProducts' => $this->productService->highlights('is_trending', 4),
            'saleProducts' => $this->productService->highlights('is_sale', 4),
            'stats' => $this->storeStats(),
        ]);
    }

    protected function storeStats(): array
    {
        $rating = (float) ProductReview::where('is_approved', true)->avg('rating');

        return [
            'products' => Product::where('is_active', true)->count(),
            'customers' => User::count(),
            'rating' => $rating > 0 ? number_format($rating, 1) : '5.0',
        ];
    }

    public function about()
    {
        return view('client.pages.about', [
            'stats' => [
                'products' => Product::where('is_active', true)->count(),
                'brands' => Branch::where('is_active', true)->has('products')->count(),
                'customers' => User::count(),
            ],
        ]);
    }

    public function thankYou()
    {
        return view('client.pages.thank-you', [
            'orderCode' => session('order_code'),
        ]);
    }
}
