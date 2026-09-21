<?php

namespace App\Services\Payment;

use App\Const\PaymentConst;
use App\Models\Order;
use App\Services\Order\OrderStateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class VnpayService
{
    public function __construct(protected OrderStateService $orderState) {}

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

        $now = now($config['timezone']);

        $params = [
            'vnp_Version' => PaymentConst::VNPAY_VERSION,
            'vnp_Command' => PaymentConst::VNPAY_COMMAND_PAY,
            'vnp_TmnCode' => $config['tmn_code'],
            'vnp_Amount' => (int) round((float) $order->total_amount * PaymentConst::VNPAY_AMOUNT_MULTIPLIER),
            'vnp_CreateDate' => $now->format(PaymentConst::VNPAY_DATE_FORMAT),
            'vnp_CurrCode' => $config['currency'],
            'vnp_IpAddr' => $clientIp,
            'vnp_Locale' => $config['locale'],
            'vnp_OrderInfo' => __('client.payment.order_info', ['code' => $order->code]),
            'vnp_OrderType' => PaymentConst::VNPAY_ORDER_TYPE,
            'vnp_ReturnUrl' => $config['return_url'],
            'vnp_TxnRef' => $reference,
            'vnp_ExpireDate' => $now->copy()->addMinutes((int) $config['expire_minutes'])->format(PaymentConst::VNPAY_DATE_FORMAT),
        ];

        ksort($params);

        $query = $this->buildHashData($params);

        return $config['endpoint'] . '?' . $query . '&vnp_SecureHash=' . $this->sign($query);
    }

    public function verify(array $payload): bool
    {
        $received = (string) ($payload['vnp_SecureHash'] ?? '');

        if ($received === '') {
            return false;
        }

        $data = $this->onlyVnpKeys($payload);
        ksort($data);

        return hash_equals($this->sign($this->buildHashData($data)), $received);
    }

    public function resolve(array $payload): array
    {
        if (! $this->verify($payload)) {
            return $this->result(PaymentConst::VNPAY_RSP_INVALID_SIGNATURE, PaymentConst::RESULT_INVALID_SIGNATURE);
        }

        $order = Order::where('payment_reference', (string) ($payload['vnp_TxnRef'] ?? ''))->first();

        if (! $order || (int) $order->payment_method !== PaymentConst::METHOD_VNPAY) {
            return $this->result(PaymentConst::VNPAY_RSP_ORDER_NOT_FOUND, PaymentConst::RESULT_ORDER_NOT_FOUND);
        }

        $paid = $order->isPaid();
        $message = match (true) {
            $paid => PaymentConst::RESULT_PAID,
            (int) $order->payment_status === PaymentConst::STATUS_REFUND_PENDING => PaymentConst::RESULT_REFUND_PENDING,
            (int) $order->payment_status === PaymentConst::STATUS_FAILED => PaymentConst::RESULT_FAILED,
            default => PaymentConst::RESULT_PENDING,
        };

        return $this->result(PaymentConst::VNPAY_SUCCESS_CODE, $message, $order, $paid);
    }

    public function settle(array $payload, string $source): array
    {
        if (! $this->isEnabled()) {
            return $this->result(PaymentConst::VNPAY_RSP_UNKNOWN_ERROR, PaymentConst::RESULT_GATEWAY_DISABLED);
        }

        if (! $this->verify($payload)) {
            return $this->result(PaymentConst::VNPAY_RSP_INVALID_SIGNATURE, PaymentConst::RESULT_INVALID_SIGNATURE);
        }

        $reference = (string) ($payload['vnp_TxnRef'] ?? '');

        return DB::transaction(function () use ($payload, $source, $reference) {
            $order = Order::where('payment_reference', $reference)->lockForUpdate()->first();

            if (! $order || (int) $order->payment_method !== PaymentConst::METHOD_VNPAY) {
                return $this->result(PaymentConst::VNPAY_RSP_ORDER_NOT_FOUND, PaymentConst::RESULT_ORDER_NOT_FOUND);
            }

            $paidAmount = (int) ($payload['vnp_Amount'] ?? 0);
            $expectedAmount = (int) round((float) $order->total_amount * PaymentConst::VNPAY_AMOUNT_MULTIPLIER);

            if ($paidAmount !== $expectedAmount) {
                $this->orderState->applyAmountMismatch($order, PaymentConst::GATEWAY_VNPAY, $this->transactionData($payload, $source));

                return $this->result(PaymentConst::VNPAY_RSP_INVALID_AMOUNT, PaymentConst::RESULT_AMOUNT_MISMATCH, $order);
            }

            $successful = ($payload['vnp_ResponseCode'] ?? null) === PaymentConst::VNPAY_SUCCESS_CODE
                && ($payload['vnp_TransactionStatus'] ?? PaymentConst::VNPAY_SUCCESS_CODE) === PaymentConst::VNPAY_SUCCESS_CODE;

            if (! $successful) {
                $result = $this->orderState->applyGatewayFailure($order, PaymentConst::GATEWAY_VNPAY, $this->transactionData($payload, $source));

                return $this->result(PaymentConst::VNPAY_SUCCESS_CODE, $result, $order);
            }

            $result = $this->orderState->applyGatewaySuccess($order, PaymentConst::GATEWAY_VNPAY, $this->transactionData($payload, $source, true));

            $code = $result === PaymentConst::RESULT_ALREADY_CONFIRMED
                ? PaymentConst::VNPAY_RSP_ALREADY_CONFIRMED
                : PaymentConst::VNPAY_SUCCESS_CODE;

            return $this->result($code, $result, $order, $result === PaymentConst::RESULT_PAID);
        });
    }

    protected function transactionData(array $payload, string $source, bool $successful = false): array
    {
        return [
            'reference' => (string) ($payload['vnp_TxnRef'] ?? ''),
            'transaction_no' => $payload['vnp_TransactionNo'] ?? null,
            'bank_code' => $payload['vnp_BankCode'] ?? null,
            'response_code' => $payload['vnp_ResponseCode'] ?? null,
            'amount' => ((int) ($payload['vnp_Amount'] ?? 0)) / PaymentConst::VNPAY_AMOUNT_MULTIPLIER,
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

    protected function makeReference(Order $order): string
    {
        return $order->code . '-' . Str::upper(Str::random(PaymentConst::REFERENCE_SUFFIX_LENGTH));
    }

    protected function onlyVnpKeys(array $payload): array
    {
        $filtered = [];

        foreach ($payload as $key => $value) {
            if (str_starts_with($key, 'vnp_') && ! in_array($key, ['vnp_SecureHash', 'vnp_SecureHashType'], true)) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }

    protected function buildHashData(array $params): string
    {
        $pairs = [];

        foreach ($params as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $pairs[] = urlencode((string) $key) . '=' . urlencode((string) $value);
        }

        return implode('&', $pairs);
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
