<?php

namespace App\Http\Controllers;

use App\Const\UserConst;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('admin.pages.dashboard', [
            'stats' => [
                'users' => User::count(),
                'products' => Product::count(),
                'categories' => Category::count(),
                'branches' => Branch::count(),
                'orders' => Order::count(),
                'revenue' => (float) Order::where('is_paid', true)->sum('total_amount'),
            ],
            'latestOrders' => Order::with('user')->latest('id')->limit(5)->get(),
            'latestUsers' => User::where('role', UserConst::ROLE_USER)->latest('id')->limit(5)->get(),
        ]);
    }
}
