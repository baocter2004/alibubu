<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tag\GetTagRequest;
use App\Http\Requests\Admin\Tag\PostTagRequest;
use App\Services\Admin\TagService;

class TagController extends Controller
{
    public function __construct(protected TagService $tagService) {}

    public function index(GetTagRequest $request)
    {
        session()->forget('tag_data');

        return view('admin.pages.tags.index', [
            'tags' => $this->tagService->search(array_merge($request->validated(), ['relates_count' => ['products']])),
        ]);
    }

    public function trash(GetTagRequest $request)
    {
        return view('admin.pages.tags.trash', [
            'tags' => $this->tagService->searchTrashed($request->validated()),
        ]);
    }

    public function create()
    {
        return view('admin.pages.tags.create', [
            'data' => session()->get('tag_data'),
        ]);
    }

    public function edit(int|string $id)
    {
        $tag = $this->tagService->find($id);

        abort_if(! $tag, 404);

        return view('admin.pages.tags.edit', compact('tag'));
    }

    public function confirm(PostTagRequest $request, $id = null)
    {
        session()->put('tag_data', $this->tagService->prepareConfirmData($request->validated(), $id));

        return redirect()->route('admin.tags.confirm-detail');
    }

    public function confirmDetail()
    {
        $data = session()->get('tag_data');

        if (! $data) {
            return redirect()->route('admin.tags.create');
        }

        return view('admin.pages.tags.confirms.form-confirm', compact('data'));
    }

    public function save()
    {
        $data = session()->get('tag_data');

        if (! $data) {
            return redirect()->route('admin.tags.create');
        }

        if (! empty($data['id'])) {
            $this->tagService->update($data['id'], $data);
            $message = __('admin/tag.messages.updated');
        } else {
            $this->tagService->create($data);
            $message = __('admin/tag.messages.created');
        }

        session()->forget('tag_data');

        return redirect()->route('admin.tags.index')->with('success', $message);
    }

    public function show(int|string $id)
    {
        $tag = $this->tagService->filter(['relates' => ['products'], 'relates_count' => ['products']])->find($id);

        abort_if(! $tag, 404);

        return view('admin.pages.tags.show', compact('tag'));
    }

    public function destroy(int|string $id)
    {
        $result = $this->tagService->delete($id);

        return redirect()
            ->route('admin.tags.index')
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function forceDestroy(int|string $id)
    {
        if (! $this->tagService->forceDelete($id)) {
            return redirect()->route('admin.tags.trash')->with('error', __('admin/tag.messages.not_found'));
        }

        return redirect()->route('admin.tags.trash')->with('success', __('admin/tag.messages.force_deleted'));
    }

    public function restore(int|string $id)
    {
        $this->tagService->restore($id);

        return redirect()->route('admin.tags.index')->with('success', __('admin/tag.messages.restored'));
    }
}
