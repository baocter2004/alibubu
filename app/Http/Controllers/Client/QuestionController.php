<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreQuestionRequest;
use App\Models\Product;
use App\Services\Client\QuestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function __construct(protected QuestionService $questionService) {}

    public function store(StoreQuestionRequest $request, string $slug): RedirectResponse
    {
        $product = Product::where('slug', $slug)->where('is_active', true)->first();

        if (! $product) {
            return back()->with('error', __('client.messages.product_not_found'));
        }

        $result = $this->questionService->ask($product, Auth::user(), $request->validated());

        return back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }
}
