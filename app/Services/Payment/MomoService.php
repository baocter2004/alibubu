<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MomoService
{
    public const SUCCESS_CODE = 0;

    public function isEnabled(): bool
    {
        $config = $this->config();

        return (bool) $config['enabled']
            && ! empty($config['partner_code'])
            && ! empty($config['access_key'])
            && ! empty($config['secret_key']);
    }

    public function createPaymentUrl(Order $order): string
    {
        if (! $this->isEnabled()) {
            throw new RuntimeException(__('client.payment.messages.gateway_disabled'));
        }

        $config = $this->config();
        $reference = $order->code . '-' . Str::upper(Str::random(6));
        $amount = (int) round((float) $order->total_amount);
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

        $response = Http::timeout(30)->acceptJson()->post($config['endpoint'], [
            'partnerCode' => $config['partner_code'],
            'partnerName' => config('app.name'),
            'storeId' => config('app.name'),
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $reference,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $config['return_url'],
            'ipnUrl' => $config['ipn_url'],
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $config['request_type'],
            'signature' => hash_hmac('sha256', $raw, $config['secret_key']),
        ]);

        $payUrl = $response->json('payUrl');

        if (! $response->successful() || ! $payUrl) {
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

    public function settle(array $payload, string $source): array
    {
        if (! $this->verify($payload)) {
            return $this->result(97, 'invalid_signature');
        }

        $order = Order::where('payment_reference', $payload['orderId'] ?? '')->first();

        if (! $order) {
            return $this->result(1, 'order_not_found');
        }

        if ((int) ($payload['amount'] ?? 0) !== (int) round((float) $order->total_amount)) {
            $this->log($order, $payload, $source, false);

            return $this->result(4, 'amount_mismatch', $order);
        }

        if ($order->is_paid) {
            return $this->result(2, 'already_confirmed', $order, true);
        }

        $successful = (int) ($payload['resultCode'] ?? -1) === self::SUCCESS_CODE;

        DB::transaction(function () use ($order, $payload, $source, $successful) {
            $this->log($order, $payload, $source, $successful);

            if ($successful) {
                $order->forceFill(['is_paid' => true, 'paid_at' => now()])->save();
            }
        });

        return $this->result(self::SUCCESS_CODE, $successful ? 'paid' : 'failed', $order, $successful);
    }

    protected function log(Order $order, array $payload, string $source, bool $successful): void
    {
        PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway' => 'momo',
            'reference' => (string) ($payload['orderId'] ?? ''),
            'transaction_no' => $payload['transId'] ?? null,
            'bank_code' => $payload['payType'] ?? null,
            'response_code' => (string) ($payload['resultCode'] ?? ''),
            'amount' => (float) ($payload['amount'] ?? 0),
            'is_successful' => $successful,
            'source' => $source,
            'payload' => $payload,
        ]);
    }

    protected function result(int $code, string $message, ?Order $order = null, bool $paid = false): array
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
