<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class VnpayService
{
    public const SUCCESS_CODE = '00';

    public function isEnabled(): bool
    {
        $config = $this->config();

        return (bool) $config['enabled']
            && ! empty($config['tmn_code'])
            && ! empty($config['hash_secret']);
    }

    public function createPaymentUrl(Order $order, string $clientIp): string
    {
        if (! $this->isEnabled()) {
            throw new RuntimeException(__('client.payment.messages.gateway_disabled'));
        }

        $config = $this->config();
        $reference = $this->makeReference($order);

        $order->forceFill(['payment_reference' => $reference])->save();

        $params = [
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'pay',
            'vnp_TmnCode' => $config['tmn_code'],
            'vnp_Amount' => (int) round((float) $order->total_amount * 100),
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_CurrCode' => $config['currency'],
            'vnp_IpAddr' => $clientIp,
            'vnp_Locale' => $config['locale'],
            'vnp_OrderInfo' => __('client.payment.order_info', ['code' => $order->code]),
            'vnp_OrderType' => 'other',
            'vnp_ReturnUrl' => $config['return_url'],
            'vnp_TxnRef' => $reference,
            'vnp_ExpireDate' => now()->addMinutes($config['expire_minutes'])->format('YmdHis'),
        ];

        ksort($params);

        $query = $this->buildQuery($params);

        return $config['endpoint'] . '?' . $query . '&vnp_SecureHash=' . $this->sign($query);
    }

    public function verify(array $payload): bool
    {
        $received = (string) ($payload['vnp_SecureHash'] ?? '');

        if ($received === '') {
            return false;
        }

        unset($payload['vnp_SecureHash'], $payload['vnp_SecureHashType']);
        ksort($payload);

        return hash_equals($this->sign($this->buildQuery($payload)), $received);
    }

    public function settle(array $payload, string $source): array
    {
        if (! $this->verify($payload)) {
            return $this->result('97', 'invalid_signature');
        }

        $order = Order::where('payment_reference', $payload['vnp_TxnRef'] ?? '')->first();

        if (! $order) {
            return $this->result('01', 'order_not_found');
        }

        $paidAmount = (int) ($payload['vnp_Amount'] ?? 0);

        if ($paidAmount !== (int) round((float) $order->total_amount * 100)) {
            $this->log($order, $payload, $source, false);

            return $this->result('04', 'amount_mismatch', $order);
        }

        $successful = ($payload['vnp_ResponseCode'] ?? null) === self::SUCCESS_CODE
            && ($payload['vnp_TransactionStatus'] ?? self::SUCCESS_CODE) === self::SUCCESS_CODE;

        if ($order->is_paid) {
            return $this->result('02', 'already_confirmed', $order, true);
        }

        DB::transaction(function () use ($order, $payload, $source, $successful) {
            $this->log($order, $payload, $source, $successful);

            if ($successful) {
                $order->forceFill([
                    'is_paid' => true,
                    'paid_at' => now(),
                ])->save();
            }
        });

        return $this->result(self::SUCCESS_CODE, $successful ? 'paid' : 'failed', $order, $successful);
    }

    protected function log(Order $order, array $payload, string $source, bool $successful): void
    {
        PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway' => 'vnpay',
            'reference' => (string) ($payload['vnp_TxnRef'] ?? ''),
            'transaction_no' => $payload['vnp_TransactionNo'] ?? null,
            'bank_code' => $payload['vnp_BankCode'] ?? null,
            'response_code' => $payload['vnp_ResponseCode'] ?? null,
            'amount' => ((int) ($payload['vnp_Amount'] ?? 0)) / 100,
            'is_successful' => $successful,
            'source' => $source,
            'payload' => $payload,
        ]);
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

    protected function makeReference(Order $order): string
    {
        return $order->code . '-' . Str::upper(Str::random(6));
    }

    protected function buildQuery(array $params): string
    {
        return http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    protected function sign(string $query): string
    {
        return hash_hmac('sha512', $query, (string) $this->config()['hash_secret']);
    }

    protected function config(): array
    {
        return config('payment.vnpay');
    }
}
