<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientAuthController extends Controller
{
    public function showFormRegister() {
        return view('client.auth.register');
    }

    public function showFormLogin() {
        return view('client.auth.login');
    }

    public function showFormForgotPassword() {
        return view('client.auth.forgot-password');
    }
}
