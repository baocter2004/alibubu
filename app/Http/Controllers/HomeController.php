<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('client.pages.index');
    }

    public function shops()
    {
        return view('client.shop');
    }

    public function shopDetail()
    {
        return view('client.shop-detail');
    }

    public function cart()
    {
        return view("client.cart");
    }
    public function checkout()
    {
        return view("client.checkout");
    }
}
