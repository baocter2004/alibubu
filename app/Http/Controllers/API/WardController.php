<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ward;

class WardController extends Controller
{
    public function getWards($provinceId)
    {
        $wards = Ward::where('province_id', $provinceId)->select('id','name', 'province_id')->get();
        return response()->json($wards);
    }
}
