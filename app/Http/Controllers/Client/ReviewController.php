<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreReviewRequest;
use App\Services\Client\ProductService;
use App\Services\Client\ReviewService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReviewController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected ReviewService $reviewService
    ) {}

    public function store(StoreReviewRequest $request, string $slug)
    {
        $product = $this->productService->findBySlug($slug);

        if (! $product) {
            throw new NotFoundHttpException(__('client.messages.product_not_found'));
        }

        $result = $this->reviewService->store($product, Auth::user(), $request->validated());

        return back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }
}
