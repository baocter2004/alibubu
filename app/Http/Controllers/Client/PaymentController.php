<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Payment\MomoService;
use App\Services\Payment\VnpayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected VnpayService $vnpayService,
        protected MomoService $momoService
    ) {}

    public function vnpayReturn(Request $request)
    {
        $result = $this->vnpayService->settle($request->query(), 'return');

        if ($result['order'] === null) {
            return redirect()
                ->route('index')
                ->with('error', __('client.payment.messages.' . $result['message']));
        }

        if ($result['paid'] || $result['message'] === 'already_confirmed') {
            return redirect()
                ->route('thanks-you')
                ->with('order_code', $result['order']->code)
                ->with('order_id', $result['order']->id)
                ->with('success', __('client.payment.messages.paid'));
        }

        return redirect()
            ->route('order.track')
            ->with('error', __('client.payment.messages.' . $result['message']));
    }

    public function momoReturn(Request $request)
    {
        return $this->finish($this->momoService->settle($request->query(), 'return'));
    }

    public function momoIpn(Request $request): JsonResponse
    {
        $result = $this->momoService->settle($request->all(), 'ipn');

        return response()->json([
            'resultCode' => $result['code'],
            'message' => $result['message'],
        ]);
    }

    protected function finish(array $result)
    {
        if ($result['order'] === null) {
            return redirect()
                ->route('index')
                ->with('error', __('client.payment.messages.' . $result['message']));
        }

        if ($result['paid'] || $result['message'] === 'already_confirmed') {
            return redirect()
                ->route('thanks-you')
                ->with('order_code', $result['order']->code)
                ->with('order_id', $result['order']->id)
                ->with('success', __('client.payment.messages.paid'));
        }

        return redirect()
            ->route('order.track')
            ->with('error', __('client.payment.messages.' . $result['message']));
    }

    public function vnpayIpn(Request $request): JsonResponse
    {
        $result = $this->vnpayService->settle($request->query(), 'ipn');

        return response()->json([
            'RspCode' => $result['code'],
            'Message' => $result['message'],
        ]);
    }
}
