<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\CategoryService;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService) {}
}
