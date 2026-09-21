<?php

namespace App\Services\Payment;

use App\Const\PaymentConst;
use App\Models\Order;
use App\Services\Order\OrderStateService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class MomoService
{
    public function __construct(protected OrderStateService $orderState) {}

    public function isEnabled(): bool
    {
        $config = $this->config();

        return (bool) $config['enabled']
            && ! empty($config['partner_code'])
            && ! empty($config['access_key'])
            && ! empty($config['secret_key']);
    }

    public function isAmountAllowed(float $amount): bool
    {
        $config = $this->config();
        $amount = (int) round($amount);

        return $amount >= (int) $config['min_amount'] && $amount <= (int) $config['max_amount'];
    }

    public function createPaymentUrl(Order $order): string
    {
        if (! $this->isEnabled()) {
            throw new RuntimeException(__('client.payment.messages.gateway_disabled'));
        }

        $amount = (int) round((float) $order->total_amount);

        if (! $this->isAmountAllowed($amount)) {
            throw new RuntimeException(__('client.payment.messages.amount_range'));
        }

        $config = $this->config();
        $reference = $order->code . '-' . Str::upper(Str::random(PaymentConst::REFERENCE_SUFFIX_LENGTH));
        $orderInfo = __('client.payment.order_info', ['code' => $order->code]);
        $requestId = (string) Str::uuid();
        $extraData = '';

        $raw = 'accessKey=' . $config['access_key']
            . '&amount=' . $amount
            . '&extraData=' . $extraData
            . '&ipnUrl=' . $config['ipn_url']
            . '&orderId=' . $reference
            . '&orderInfo=' . $orderInfo
            . '&partnerCode=' . $config['partner_code']
            . '&redirectUrl=' . $config['return_url']
            . '&requestId=' . $requestId
            . '&requestType=' . $config['request_type'];

        try {
            $response = Http::connectTimeout((int) $config['connect_timeout'])
                ->timeout((int) $config['timeout'])
                ->acceptJson()
                ->post($config['endpoint'], [
                    'partnerCode' => $config['partner_code'],
                    'partnerName' => config('app.name'),
                    'storeId' => config('app.name'),
                    'requestId' => $requestId,
                    'amount' => $amount,
                    'orderId' => $reference,
                    'orderInfo' => $orderInfo,
                    'redirectUrl' => $config['return_url'],
                    'ipnUrl' => $config['ipn_url'],
                    'lang' => $config['lang'],
                    'extraData' => $extraData,
                    'requestType' => $config['request_type'],
                    'signature' => hash_hmac('sha256', $raw, $config['secret_key']),
                ]);
        } catch (ConnectionException $e) {
            Log::error(__METHOD__, ['message' => $e->getMessage(), 'order_code' => $order->code]);

            throw new RuntimeException(__('client.payment.messages.gateway_unavailable'));
        }

        $payUrl = $response->json('payUrl');

        if (! $response->successful() || ! $payUrl) {
            Log::error(__METHOD__, [
                'status' => $response->status(),
                'resultCode' => $response->json('resultCode'),
                'message' => $response->json('message'),
                'order_code' => $order->code,
            ]);

            throw new RuntimeException(__('client.payment.messages.gateway_unavailable'));
        }

        $order->forceFill(['payment_reference' => $reference])->save();

        return $payUrl;
    }

    public function verify(array $payload): bool
    {
        $config = $this->config();
        $received = (string) ($payload['signature'] ?? '');

        if ($received === '') {
            return false;
        }

        $raw = 'accessKey=' . $config['access_key']
            . '&amount=' . ($payload['amount'] ?? '')
            . '&extraData=' . ($payload['extraData'] ?? '')
            . '&message=' . ($payload['message'] ?? '')
            . '&orderId=' . ($payload['orderId'] ?? '')
            . '&orderInfo=' . ($payload['orderInfo'] ?? '')
            . '&orderType=' . ($payload['orderType'] ?? '')
            . '&partnerCode=' . ($payload['partnerCode'] ?? '')
            . '&payType=' . ($payload['payType'] ?? '')
            . '&requestId=' . ($payload['requestId'] ?? '')
            . '&responseTime=' . ($payload['responseTime'] ?? '')
            . '&resultCode=' . ($payload['resultCode'] ?? '')
            . '&transId=' . ($payload['transId'] ?? '');

        return hash_equals(hash_hmac('sha256', $raw, $config['secret_key']), $received);
    }

    public function resolve(array $payload): array
    {
        if (! $this->verify($payload)) {
            return $this->result((string) 97, PaymentConst::RESULT_INVALID_SIGNATURE);
        }

        $order = Order::where('payment_reference', (string) ($payload['orderId'] ?? ''))->first();

        if (! $order || (int) $order->payment_method !== PaymentConst::METHOD_MOMO) {
            return $this->result((string) 1, PaymentConst::RESULT_ORDER_NOT_FOUND);
        }

        $paid = $order->isPaid();
        $message = match (true) {
            $paid => PaymentConst::RESULT_PAID,
            (int) $order->payment_status === PaymentConst::STATUS_REFUND_PENDING => PaymentConst::RESULT_REFUND_PENDING,
            (int) $order->payment_status === PaymentConst::STATUS_FAILED => PaymentConst::RESULT_FAILED,
            default => PaymentConst::RESULT_PENDING,
        };

        return $this->result((string) PaymentConst::MOMO_SUCCESS_CODE, $message, $order, $paid);
    }

    public function settle(array $payload, string $source): array
    {
        if (! $this->isEnabled()) {
            return $this->result((string) 99, PaymentConst::RESULT_GATEWAY_DISABLED);
        }

        if (! $this->verify($payload)) {
            return $this->result((string) 97, PaymentConst::RESULT_INVALID_SIGNATURE);
        }

        $reference = (string) ($payload['orderId'] ?? '');

        return DB::transaction(function () use ($payload, $source, $reference) {
            $order = Order::where('payment_reference', $reference)->lockForUpdate()->first();

            if (! $order || (int) $order->payment_method !== PaymentConst::METHOD_MOMO) {
                return $this->result((string) 1, PaymentConst::RESULT_ORDER_NOT_FOUND);
            }

            $paidAmount = (int) ($payload['amount'] ?? 0);
            $expectedAmount = (int) round((float) $order->total_amount);

            if ($paidAmount !== $expectedAmount) {
                $this->orderState->applyAmountMismatch($order, PaymentConst::GATEWAY_MOMO, $this->transactionData($payload, $source));

                return $this->result((string) 4, PaymentConst::RESULT_AMOUNT_MISMATCH, $order);
            }

            $successful = (int) ($payload['resultCode'] ?? -1) === PaymentConst::MOMO_SUCCESS_CODE;

            if (! $successful) {
                $result = $this->orderState->applyGatewayFailure($order, PaymentConst::GATEWAY_MOMO, $this->transactionData($payload, $source));

                return $this->result((string) PaymentConst::MOMO_SUCCESS_CODE, $result, $order);
            }

            $result = $this->orderState->applyGatewaySuccess($order, PaymentConst::GATEWAY_MOMO, $this->transactionData($payload, $source, true));

            return $this->result((string) PaymentConst::MOMO_SUCCESS_CODE, $result, $order, $result === PaymentConst::RESULT_PAID);
        });
    }

    protected function transactionData(array $payload, string $source, bool $successful = false): array
    {
        return [
            'reference' => (string) ($payload['orderId'] ?? ''),
            'transaction_no' => $payload['transId'] ?? null,
            'bank_code' => $payload['payType'] ?? null,
            'response_code' => (string) ($payload['resultCode'] ?? ''),
            'amount' => (float) ($payload['amount'] ?? 0),
            'source' => $source,
            'payload' => $payload,
            'is_successful' => $successful,
        ];
    }

    protected function result(string $code, string $message, ?Order $order = null, bool $paid = false): array
    {
        return [
            'code' => $code,
            'message' => $message,
            'order' => $order,
            'paid' => $paid,
        ];
    }

    protected function config(): array
    {
        return config('payment.momo');
    }
}
