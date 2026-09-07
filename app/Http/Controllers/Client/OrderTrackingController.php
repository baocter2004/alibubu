<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\TrackOrderRequest;
use App\Models\Order;

class OrderTrackingController extends Controller
{
    public function show()
    {
        return view('client.pages.track-order');
    }

    public function lookup(TrackOrderRequest $request)
    {
        $data = $request->validated();

        $order = Order::query()
            ->with(['items.product'])
            ->whereRaw('UPPER(code) = ?', [mb_strtoupper(trim($data['code']))])
            ->where('phone_number', $data['phone_number'])
            ->first();

        if (! $order) {
            return back()
                ->withInput()
                ->with('error', __('client.tracking.messages.not_found'));
        }

        return view('client.pages.track-order', compact('order'));
    }
}
