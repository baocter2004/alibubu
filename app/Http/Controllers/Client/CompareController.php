<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Client\CompareService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class CompareController extends Controller
{
    public function __construct(protected CompareService $compareService) {}

    public function index()
    {
        $products = $this->compareService->products();

        return view('client.pages.compare.index', [
            'products' => $products,
            'sections' => $this->compareService->matrix($products),
            'maxItems' => CompareService::MAX_ITEMS,
        ]);
    }

    public function toggle(Request $request, string $slug)
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with('categories')
            ->first();

        if (! $product) {
            return $this->respond($request, false, __('client.messages.product_not_found'), 404);
        }

        try {
            $added = $this->compareService->toggle($product);
        } catch (RuntimeException $exception) {
            return $this->respond($request, false, $exception->getMessage(), 422);
        }

        return $this->respond(
            $request,
            true,
            __('client.compare.messages.' . ($added ? 'added' : 'removed')),
            200,
            ['compared' => $added]
        );
    }

    public function destroy(Request $request, string $id)
    {
        $this->compareService->remove($id);

        return $this->respond($request, true, __('client.compare.messages.removed'));
    }

    public function clear(Request $request)
    {
        $this->compareService->clear();

        return $this->respond($request, true, __('client.compare.messages.cleared'));
    }

    protected function respond(Request $request, bool $status, string $message, int $code = 200, array $extra = [])
    {
        if ($request->expectsJson()) {
            return response()->json(array_merge([
                'status' => $status,
                'message' => $message,
                'count' => $this->compareService->count(),
                'items' => $this->compareService->summary()->map(fn (Product $product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'url' => route('shop.show', $product->slug),
                    'thumbnail' => $product->thumbnail
                        ? Storage::disk('public')->url($product->thumbnail)
                        : null,
                ])->all(),
            ], $extra), $code);
        }

        return back()->with($status ? 'success' : 'error', $message);
    }
}
