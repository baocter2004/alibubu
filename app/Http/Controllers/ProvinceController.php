<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\GetProvinceRequest;
use App\Services\ProvinceService;

class ProvinceController extends Controller
{
    public function __construct(protected ProvinceService $provinceService) {}

    public function index(GetProvinceRequest $getProvinceRequest) {
        $provinces = $this->provinceService->search($getProvinceRequest->validated());
        return view('admin.pages.provinces.index', compact('provinces'));
    }
}
