<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\GetWardRequest;
use App\Services\WardService;

class WardController extends Controller
{
    public function __construct(protected WardService $wardService) {}

    public function index(GetWardRequest $getWardRequest)
    {
        $wards = $this->wardService->search(array_merge($getWardRequest->validated(), ['relates' => ['province'], 'relates_count' => ['userAddresses']]));
        return view('admin.pages.wards.index', compact('wards'));
    }
}
