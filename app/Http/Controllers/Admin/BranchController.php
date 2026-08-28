<?php

namespace App\Http\Controllers\Admin;

use App\Const\GlobalConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Branch\GetBranchRequest;
use App\Http\Requests\Admin\Branch\PostBranchRequest;
use App\Services\Admin\BranchService;

class BranchController extends Controller
{
    public function __construct(protected BranchService $branchService) {}

    public function index(GetBranchRequest $getBranchRequest)
    {
        session()->forget('branch_data');
        $branches = $this->branchService->paginate(array_merge($getBranchRequest->validated(), ['relates_count' => ['products']]));
        $statuses = GlobalConst::STATUS;

        return view('admin.pages.branches.index', compact('branches', 'statuses'));
    }

    public function trash(GetBranchRequest $request)
    {
        $branches = $this->branchService->searchTrashed($request->validated());
        $statuses = GlobalConst::STATUS;

        return view('admin.pages.branches.trash', compact('branches', 'statuses'));
    }

    public function create()
    {
        $data = session()->get('branch_data');

        return view('admin.pages.branches.create', compact('data'));
    }

    public function edit(int|string $id)
    {
        $branch = $this->branchService->find($id);

        return view('admin.pages.branches.edit', compact('branch'));
    }

    public function confirm(PostBranchRequest $request, $id = null)
    {
        $oldData = session()->get('branch_data');
        $data = $this->branchService->prepareConfirmData($request->validated(), $id, $oldData);

        session()->put('branch_data', $data);

        return redirect()->route('admin.branches.confirm-detail');
    }

    public function confirmDetail()
    {
        $data = session()->get('branch_data');

        if (! $data) {
            return redirect()->route('admin.branches.create');
        }

        return view('admin.pages.branches.confirms.form-confirm', compact('data'));
    }

    public function save()
    {
        $data = session()->get('branch_data');

        if (! $data) {
            return redirect()->route('admin.branches.create');
        }

        if (! empty($data['id'])) {
            $this->branchService->update($data['id'], $data);
            $message = __('admin/branch.messages.updated');
        } else {
            $this->branchService->create($data);
            $message = __('admin/branch.messages.created');
        }

        session()->forget('branch_data');

        return redirect()->route('admin.branches.index')->with('success', $message);
    }

    public function show(int|string $id)
    {
        $branch = $this->branchService->filter(['relates_count' => ['products']])->find($id);

        return view('admin.pages.branches.show', compact('branch'));
    }

    public function destroy(int|string $id)
    {
        $result = $this->branchService->delete($id);

        if (! $result['status']) {
            return redirect()->route('admin.branches.index')->with('error', $result['message']);
        }

        return redirect()->route('admin.branches.index')->with('success', $result['message']);
    }

    public function forceDestroy(int|string $id)
    {
        $this->branchService->forceDelete($id);

        return redirect()->route('admin.branches.index')->with('success', __('admin/branch.messages.force_deleted'));
    }

    public function restore(int|string $id)
    {
        $this->branchService->restore($id);

        return redirect()->route('admin.branches.index')->with('success', __('admin/branch.messages.restored'));
    }
}
