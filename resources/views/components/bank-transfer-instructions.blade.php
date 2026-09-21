@if ((int) $order->payment_method === \App\Const\PaymentConst::METHOD_BANK_TRANSFER && ! $order->isPaid() && config('payment.bank_transfer.enabled'))
    @php
        $bankCode = (string) config('payment.bank_transfer.bank_code');
        $accountNumber = (string) config('payment.bank_transfer.account_number');
        $accountName = (string) config('payment.bank_transfer.account_name');
        $qrUrl = config('payment.bank_transfer.qr_enabled')
            ? \App\Const\BankConst::vietQrUrl($bankCode, $accountNumber, (float) $order->total_amount, $order->code, $accountName, (string) config('payment.bank_transfer.qr_template', 'compact2'))
            : null;
    @endphp

    <div class="p-4 bg-muted/50 border border-border rounded-xl text-sm">
        <p class="font-semibold text-foreground mb-1">{{ __('client.mail.order.bank_transfer_title') }}</p>
        <p class="text-muted-foreground mb-3">{{ __('client.mail.order.bank_transfer_hint') }}</p>

        <div class="flex flex-col sm:flex-row gap-4">
            <dl class="grid grid-cols-3 gap-y-2 flex-1">
                <dt class="text-muted-foreground">{{ __('client.mail.order.bank_name') }}</dt>
                <dd class="col-span-2 text-foreground font-medium">{{ \App\Const\BankConst::getShortName($bankCode) }}</dd>
                <dt class="text-muted-foreground">{{ __('client.mail.order.bank_account_number') }}</dt>
                <dd class="col-span-2 text-foreground font-medium">{{ $accountNumber }}</dd>
                <dt class="text-muted-foreground">{{ __('client.mail.order.bank_account_name') }}</dt>
                <dd class="col-span-2 text-foreground font-medium">{{ $accountName }}</dd>
                <dt class="text-muted-foreground">{{ __('client.mail.order.transfer_note') }}</dt>
                <dd class="col-span-2 text-price font-bold">{{ $order->code }}</dd>
            </dl>

            @if ($qrUrl)
                <img src="{{ $qrUrl }}" alt="{{ __('client.mail.order.bank_transfer_title') }}"
                    class="w-36 h-36 rounded-lg border border-border self-center sm:self-start" loading="lazy">
            @endif
        </div>
    </div>
@endif
