<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\WardService;

class WardController extends Controller
{
    public function __construct(protected WardService $wardService) {}
}
