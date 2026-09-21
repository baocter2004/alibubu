<?php

namespace App\Http\Controllers\Client;

use App\Const\PaymentConst;
use App\Http\Controllers\Controller;
use App\Services\Payment\MomoService;
use App\Services\Payment\VnpayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(
        protected VnpayService $vnpayService,
        protected MomoService $momoService
    ) {}

    public function vnpayReturn(Request $request)
    {
        $result = config('payment.settle_on_return')
            ? $this->vnpayService->settle($request->query(), PaymentConst::SOURCE_RETURN)
            : $this->vnpayService->resolve($request->query());

        return $this->finish($result);
    }

    public function momoReturn(Request $request)
    {
        $result = config('payment.settle_on_return')
            ? $this->momoService->settle($request->query(), PaymentConst::SOURCE_RETURN)
            : $this->momoService->resolve($request->query());

        return $this->finish($result);
    }

    public function momoIpn(Request $request): Response
    {
        try {
            $this->momoService->settle($request->all(), PaymentConst::SOURCE_IPN);
        } catch (Throwable $th) {
            Log::error(__METHOD__, ['message' => $th->getMessage()]);
        }

        return response()->noContent();
    }

    public function vnpayIpn(Request $request): JsonResponse
    {
        try {
            $result = $this->vnpayService->settle($request->query(), PaymentConst::SOURCE_IPN);

            return response()->json([
                'RspCode' => $result['code'],
                'Message' => $result['message'],
            ]);
        } catch (Throwable $th) {
            Log::error(__METHOD__, ['message' => $th->getMessage()]);

            return response()->json([
                'RspCode' => PaymentConst::VNPAY_RSP_UNKNOWN_ERROR,
                'Message' => PaymentConst::RESULT_ERROR,
            ]);
        }
    }

    protected function finish(array $result)
    {
        if ($result['order'] === null) {
            return redirect()
                ->route('index')
                ->with('error', __('client.payment.messages.' . $result['message']));
        }

        if ($result['paid'] || $result['message'] === PaymentConst::RESULT_ALREADY_CONFIRMED) {
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
}
