<?php

namespace App\Http\Controllers\Client;

use App\Const\PaymentConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\TrackOrderRequest;
use App\Models\Order;
use App\Services\Payment\MomoService;
use App\Services\Payment\VnpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderTrackingController extends Controller
{
    public function __construct(
        protected VnpayService $vnpayService,
        protected MomoService $momoService
    ) {}

    public function show()
    {
        return view('client.pages.track-order');
    }

    public function lookup(TrackOrderRequest $request)
    {
        $order = $this->findOrder($request->validated());

        if (! $order) {
            return back()
                ->withInput()
                ->with('error', __('client.tracking.messages.not_found'));
        }

        return view('client.pages.track-order', compact('order'));
    }

    public function payAgain(TrackOrderRequest $request)
    {
        $data = $request->validated();
        $order = $this->findOrder($data);

        if (! $order || ! $order->canPayOnline()) {
            return back()
                ->withInput()
                ->with('error', __('client.payment.messages.not_payable'));
        }

        try {
            $url = (int) $order->payment_method === PaymentConst::METHOD_VNPAY
                ? $this->vnpayService->createPaymentUrl($order, $request->ip())
                : $this->momoService->createPaymentUrl($order);

            return redirect()->away($url);
        } catch (Throwable $th) {
            Log::error(__METHOD__, ['message' => $th->getMessage(), 'order_code' => $order->code]);

            return view('client.pages.track-order', ['order' => $order, 'error' => $th->getMessage()]);
        }
    }

    protected function findOrder(array $data): ?Order
    {
        return Order::query()
            ->with(['items.product'])
            ->whereRaw('UPPER(code) = ?', [mb_strtoupper(trim($data['code']))])
            ->where('phone_number', $data['phone_number'])
            ->first();
    }
}
