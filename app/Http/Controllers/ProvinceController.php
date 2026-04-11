<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ProvinceService;

class ProvinceController extends Controller
{
    public function __construct(protected ProvinceService $provinceService) {}
}
