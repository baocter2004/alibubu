<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\GetWardRequest;
use App\Repositories\WardService;

class WardController extends Controller
{
    public function __construct(protected WardService $wardService) {}

    public function index(GetWardRequest $getWardRequest)
    {
        $wards = $this->wardService->search($getWardRequest->validated());
        return view('admin.pages.wards.index');
    }
}
