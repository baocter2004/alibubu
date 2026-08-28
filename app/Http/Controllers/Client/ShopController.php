<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\GetProductRequest;
use App\Models\Branch;
use App\Models\Category;
use App\Services\Client\ProductService;
use App\Services\Client\ReviewService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShopController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected ReviewService $reviewService
    ) {}

    public function index(GetProductRequest $request)
    {
        $filters = $request->validated();

        return view('client.pages.shop.index', [
            'products' => $this->productService->searchForShop($filters),
            'categories' => Category::query()->where('is_active', true)->orderBy('ordinal')->get(),
            'branches' => Branch::query()->where('is_active', true)->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function show(string $slug)
    {
        $product = $this->productService->findBySlug($slug);

        if (! $product) {
            throw new NotFoundHttpException(__('client.messages.product_not_found'));
        }

        $product->loadMissing('specifications');
        $product->increment('views');

        return view('client.pages.shop.show', [
            'product' => $product,
            'relatedProducts' => $this->productService->related($product),
            'reviews' => $this->reviewService->paginateFor($product),
            'ratingBreakdown' => $this->reviewService->breakdownFor($product),
            'canReview' => $this->reviewService->canReview($product, auth()->user()),
        ]);
    }
}
