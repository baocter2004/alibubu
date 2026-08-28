<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function __invoke(Request $request, string $locale)
    {
        if (in_array($locale, config('app.supported_locales', []), true)) {
            $request->session()->put('locale', $locale);
        }

        return back();
    }
}
