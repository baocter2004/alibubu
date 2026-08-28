<?php

namespace App\Http\Controllers\Admin;

use App\Const\GlobalConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Attribute\GetAttributeRequest;
use App\Http\Requests\Admin\Attribute\PostAttributeRequest;
use App\Services\Admin\AttributeService;

class AttributeController extends Controller
{
    public function __construct(protected AttributeService $attributeService) {}

    public function index(GetAttributeRequest $request)
    {
        session()->forget('attribute_data');

        return view('admin.pages.attributes.index', [
            'attributes' => $this->attributeService->search(
                array_merge($request->validated(), ['relates_count' => ['values']])
            ),
            'statuses' => GlobalConst::statuses(),
        ]);
    }

    public function trash(GetAttributeRequest $request)
    {
        return view('admin.pages.attributes.trash', [
            'attributes' => $this->attributeService->searchTrashed($request->validated()),
        ]);
    }

    public function create()
    {
        return view('admin.pages.attributes.create', [
            'data' => session()->get('attribute_data'),
        ]);
    }

    public function edit(int|string $id)
    {
        $attribute = $this->attributeService->filter(['relates' => ['values']])->find($id);

        abort_if(! $attribute, 404);

        return view('admin.pages.attributes.edit', compact('attribute'));
    }

    public function confirm(PostAttributeRequest $request, $id = null)
    {
        session()->put('attribute_data', $this->attributeService->prepareConfirmData($request->validated(), $id));

        return redirect()->route('admin.attributes.confirm-detail');
    }

    public function confirmDetail()
    {
        $data = session()->get('attribute_data');

        if (! $data) {
            return redirect()->route('admin.attributes.create');
        }

        return view('admin.pages.attributes.confirms.form-confirm', compact('data'));
    }

    public function save()
    {
        $data = session()->get('attribute_data');

        if (! $data) {
            return redirect()->route('admin.attributes.create');
        }

        if (! empty($data['id'])) {
            $this->attributeService->update($data['id'], $data);
            $message = __('admin/attribute.messages.updated');
        } else {
            $this->attributeService->create($data);
            $message = __('admin/attribute.messages.created');
        }

        session()->forget('attribute_data');

        return redirect()->route('admin.attributes.index')->with('success', $message);
    }

    public function show(int|string $id)
    {
        $attribute = $this->attributeService->filter(['relates' => ['values']])->find($id);

        abort_if(! $attribute, 404);

        return view('admin.pages.attributes.show', compact('attribute'));
    }

    public function destroy(int|string $id)
    {
        $result = $this->attributeService->delete($id);

        return redirect()
            ->route('admin.attributes.index')
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function forceDestroy(int|string $id)
    {
        if (! $this->attributeService->forceDelete($id)) {
            return redirect()
                ->route('admin.attributes.trash')
                ->with('error', __('admin/attribute.messages.not_found'));
        }

        return redirect()
            ->route('admin.attributes.trash')
            ->with('success', __('admin/attribute.messages.force_deleted'));
    }

    public function restore(int|string $id)
    {
        $this->attributeService->restore($id);

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', __('admin/attribute.messages.restored'));
    }
}
