<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\GetProvinceRequest;
use App\Services\ProvinceService;

class ProvinceController extends Controller
{
    public function __construct(protected ProvinceService $provinceService) {}

    public function index(GetProvinceRequest $getProvinceRequest)
    {
        $provinces = $this->provinceService->search(array_merge($getProvinceRequest->validated(), ['relates_count' => ['wards', 'userAddresses']]));
        return view('admin.pages.provinces.index', compact('provinces'));
    }

    public function show(int|string $id)
    {
        $province = $this->provinceService->find($id);
        $wards = $province->wards()->paginate(10);

        return view('admin.pages.provinces.show', compact('province', 'wards'));
    }
}
