<?php

namespace App\Http\Controllers\Admin;

use App\Const\GlobalConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\GetProductRequest;
use App\Http\Requests\Admin\Product\PostProductRequest;
use App\Models\Attribute;
use App\Models\Branch;
use App\Models\Category;
use App\Services\Admin\ProductService;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function index(GetProductRequest $request)
    {
        session()->forget('product_data');

        return view('admin.pages.products.index', array_merge($this->formOptions(), [
            'products' => $this->productService->search(
                array_merge($request->validated(), ['relates' => ['branch', 'categories']])
            ),
            'statuses' => GlobalConst::statuses(),
        ]));
    }

    public function trash(GetProductRequest $request)
    {
        return view('admin.pages.products.trash', [
            'products' => $this->productService->searchTrashed($request->validated()),
        ]);
    }

    public function create()
    {
        return view('admin.pages.products.create', array_merge($this->formOptions(), [
            'data' => session()->get('product_data'),
        ]));
    }

    public function edit(int|string $id)
    {
        $product = $this->productService->filter(['relates' => ['categories']])->find($id);

        abort_if(! $product, 404);

        return view('admin.pages.products.edit', array_merge($this->formOptions(), [
            'product' => $product,
        ]));
    }

    public function confirm(PostProductRequest $request, $id = null)
    {
        $data = $this->productService->prepareConfirmData(
            $request->validated(),
            $id,
            session()->get('product_data')
        );

        session()->put('product_data', $data);

        return redirect()->route('admin.products.confirm-detail');
    }

    public function confirmDetail()
    {
        $data = session()->get('product_data');

        if (! $data) {
            return redirect()->route('admin.products.create');
        }

        return view('admin.pages.products.confirms.form-confirm', array_merge($this->formOptions(), [
            'data' => $data,
        ]));
    }

    public function save()
    {
        $data = session()->get('product_data');

        if (! $data) {
            return redirect()->route('admin.products.create');
        }

        if (! empty($data['id'])) {
            $this->productService->update($data['id'], $data);
            $message = __('admin/product.messages.updated');
        } else {
            $this->productService->create($data);
            $message = __('admin/product.messages.created');
        }

        session()->forget('product_data');

        return redirect()->route('admin.products.index')->with('success', $message);
    }

    public function show(int|string $id)
    {
        $product = $this->productService
            ->filter(['relates' => ['branch', 'categories', 'tags', 'variants', 'galleries']])
            ->find($id);

        abort_if(! $product, 404);

        return view('admin.pages.products.show', compact('product'));
    }

    public function destroy(int|string $id)
    {
        $result = $this->productService->delete($id);

        return redirect()
            ->route('admin.products.index')
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function forceDestroy(int|string $id)
    {
        if (! $this->productService->forceDelete($id)) {
            return redirect()
                ->route('admin.products.trash')
                ->with('error', __('admin/product.messages.not_found'));
        }

        return redirect()
            ->route('admin.products.trash')
            ->with('success', __('admin/product.messages.force_deleted'));
    }

    public function restore(int|string $id)
    {
        $this->productService->restore($id);

        return redirect()
            ->route('admin.products.index')
            ->with('success', __('admin/product.messages.restored'));
    }

    protected function formOptions(): array
    {
        return [
            'branches' => Branch::orderBy('name')->pluck('name', 'id'),
            'categories' => Category::orderBy('name')->pluck('name', 'id'),
            'attributeGroups' => Attribute::with(['values' => fn ($query) => $query->where('is_active', true)])
                ->orderBy('name')
                ->get()
                ->mapWithKeys(fn ($attribute) => [$attribute->name => $attribute->values->pluck('value', 'id')])
                ->filter(fn ($values) => $values->isNotEmpty()),
        ];
    }
}
