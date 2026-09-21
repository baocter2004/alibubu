<?php

namespace App\Http\Requests\Client;

use App\Const\PaymentConst;
use App\Services\Payment\MomoService;
use App\Services\Payment\VnpayService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fullname' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^0[0-9]{8,10}$/'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'integer', Rule::in($this->enabledPaymentMethods())],
        ];
    }

    protected function enabledPaymentMethods(): array
    {
        $methods = [PaymentConst::METHOD_COD];

        if ((bool) config('payment.bank_transfer.enabled')) {
            $methods[] = PaymentConst::METHOD_BANK_TRANSFER;
        }

        if (app(VnpayService::class)->isEnabled()) {
            $methods[] = PaymentConst::METHOD_VNPAY;
        }

        if (app(MomoService::class)->isEnabled()) {
            $methods[] = PaymentConst::METHOD_MOMO;
        }

        return $methods;
    }

    public function messages(): array
    {
        return [
            'phone_number.regex' => __('client.messages.phone_invalid'),
            'payment_method.in' => __('client.payment.messages.method_disabled'),
        ];
    }

    public function attributes(): array
    {
        return [
            'fullname' => __('client.checkout.fullname'),
            'phone_number' => __('client.checkout.phone_number'),
            'email' => __('client.checkout.email'),
            'address' => __('client.checkout.address'),
            'note' => __('client.checkout.note'),
        ];
    }
}
