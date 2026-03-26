<?php

namespace App\Http\Middleware;

use App\Const\UserConst;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user->role === UserConst::ROLE_ADMIN) {
            return $next($request);
        }

        return redirect()->route('auth.admin.showFormLogin')->with('error','Không Có Quyền Truy Cập !');
    }
}
