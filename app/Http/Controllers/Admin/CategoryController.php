<?php

namespace App\Http\Controllers\Admin;

use App\Const\GlobalConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\GetCategoryRequest;
use App\Http\Requests\Admin\Category\PostCategoryRequest;
use App\Services\Admin\CategoryService;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService) {}

    public function index(GetCategoryRequest $request)
    {
        session()->forget('category_data');

        return view('admin.pages.categories.index', [
            'categories' => $this->categoryService->search(
                array_merge($request->validated(), ['relates' => ['parent'], 'relates_count' => ['products', 'children']])
            ),
            'parents' => $this->categoryService->selectableParents(),
            'statuses' => GlobalConst::statuses(),
        ]);
    }

    public function trash(GetCategoryRequest $request)
    {
        return view('admin.pages.categories.trash', [
            'categories' => $this->categoryService->searchTrashed($request->validated()),
            'statuses' => GlobalConst::statuses(),
        ]);
    }

    public function create()
    {
        return view('admin.pages.categories.create', [
            'data' => session()->get('category_data'),
            'parents' => $this->categoryService->selectableParents(),
        ]);
    }

    public function edit(int|string $id)
    {
        $category = $this->categoryService->find($id);

        abort_if(! $category, 404);

        return view('admin.pages.categories.edit', [
            'category' => $category,
            'parents' => $this->categoryService->selectableParents($id),
        ]);
    }

    public function confirm(PostCategoryRequest $request, $id = null)
    {
        session()->put('category_data', $this->categoryService->prepareConfirmData($request->validated(), $id));

        return redirect()->route('admin.categories.confirm-detail');
    }

    public function confirmDetail()
    {
        $data = session()->get('category_data');

        if (! $data) {
            return redirect()->route('admin.categories.create');
        }

        return view('admin.pages.categories.confirms.form-confirm', [
            'data' => $data,
            'parents' => $this->categoryService->selectableParents(),
        ]);
    }

    public function save()
    {
        $data = session()->get('category_data');

        if (! $data) {
            return redirect()->route('admin.categories.create');
        }

        if (! empty($data['id'])) {
            $this->categoryService->update($data['id'], $data);
            $message = __('admin/category.messages.updated');
        } else {
            $this->categoryService->create($data);
            $message = __('admin/category.messages.created');
        }

        session()->forget('category_data');

        return redirect()->route('admin.categories.index')->with('success', $message);
    }

    public function show(int|string $id)
    {
        $category = $this->categoryService
            ->filter(['relates' => ['parent', 'children'], 'relates_count' => ['products']])
            ->find($id);

        abort_if(! $category, 404);

        return view('admin.pages.categories.show', compact('category'));
    }

    public function destroy(int|string $id)
    {
        $result = $this->categoryService->delete($id);

        return redirect()
            ->route('admin.categories.index')
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function forceDestroy(int|string $id)
    {
        if (! $this->categoryService->forceDelete($id)) {
            return redirect()
                ->route('admin.categories.trash')
                ->with('error', __('admin/category.messages.not_found'));
        }

        return redirect()
            ->route('admin.categories.trash')
            ->with('success', __('admin/category.messages.force_deleted'));
    }

    public function restore(int|string $id)
    {
        $this->categoryService->restore($id);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', __('admin/category.messages.restored'));
    }
}
