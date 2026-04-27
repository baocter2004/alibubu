<?php

namespace App\Http\Controllers\Admin;

use App\Const\GlobalConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Branch\GetBranchRequest;
use App\Services\Admin\BranchService;

class BranchController extends Controller
{
    public function __construct(protected BranchService $branchService) {}

    public function index(GetBranchRequest $getBranchRequest)
    {
        $branches = $this->branchService->paginate(array_merge([$getBranchRequest->validated(), ['relates_count' => 'products']]));
        $statuses = GlobalConst::STATUS;

        return view('admin.pages.branches.index', compact('branches','statuses'));
    }

    public function show(int|string $id)
    {
        $branch = $this->branchService->filter(['relates_count' => 'products'])->find($id);

        return view('admin.pages.branches.show', compact('branch'));
    }
}
