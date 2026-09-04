@extends('components.mails.layouts')

@section('title', __('client.mail.order.title'))

@section('content')
    <p>{{ __('client.mail.order.greeting', ['name' => $order->fullname]) }}</p>

    <p>{{ __('client.mail.order.intro') }}</p>

    <table width="100%" cellpadding="0" cellspacing="0"
        style="margin:20px 0; background:#f9fafb; border-radius:6px;">
        <tr>
            <td style="padding:16px;">
                <p style="margin:0 0 6px; font-size:13px; color:#6b7280;">
                    {{ __('client.mail.order.code') }}
                </p>
                <p style="margin:0; font-size:18px; font-weight:bold; color:#111;">{{ $order->code }}</p>
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; font-size:13px;">
        <tr>
            <th align="left" style="padding:8px 0; border-bottom:2px solid #e5e7eb; color:#6b7280;">
                {{ __('client.mail.order.item') }}
            </th>
            <th align="center" style="padding:8px 0; border-bottom:2px solid #e5e7eb; color:#6b7280;">
                {{ __('client.mail.order.quantity') }}
            </th>
            <th align="right" style="padding:8px 0; border-bottom:2px solid #e5e7eb; color:#6b7280;">
                {{ __('client.mail.order.subtotal') }}
            </th>
        </tr>

        @foreach ($order->items as $item)
            <tr>
                <td style="padding:10px 0; border-bottom:1px solid #f3f4f6;">
                    {{ $item->name }}
                    @if ($item->attributes_variant)
                        <br><span style="color:#9ca3af; font-size:12px;">
                            {{ implode(' / ', (array) $item->attributes_variant) }}
                        </span>
                    @endif
                </td>
                <td align="center" style="padding:10px 0; border-bottom:1px solid #f3f4f6;">
                    {{ $item->quantity }}
                </td>
                <td align="right" style="padding:10px 0; border-bottom:1px solid #f3f4f6; white-space:nowrap;">
                    {{ format_price($item->price * $item->quantity) }}
                </td>
            </tr>
        @endforeach

        @if ($order->coupon_code)
            <tr>
                <td colspan="2" style="padding:10px 0; color:#6b7280;">
                    {{ __('client.mail.order.discount') }} ({{ $order->coupon_code }})
                </td>
                <td align="right" style="padding:10px 0; color:#16a34a; white-space:nowrap;">
                    -{{ format_price($order->coupon_discount_value) }}
                </td>
            </tr>
        @endif

        <tr>
            <td colspan="2" style="padding:12px 0; font-weight:bold; border-top:2px solid #e5e7eb;">
                {{ __('client.mail.order.total') }}
            </td>
            <td align="right"
                style="padding:12px 0; font-weight:bold; font-size:16px; color:#2563eb; border-top:2px solid #e5e7eb; white-space:nowrap;">
                {{ format_price($order->total_amount) }}
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px; font-size:13px;">
        @foreach ([['client.mail.order.recipient', $order->fullname], ['client.mail.order.phone_number', $order->phone_number], ['client.mail.order.address', $order->address], ['client.mail.order.payment_method', \App\Const\PaymentConst::methodLabel($order->payment_method)]] as [$labelKey, $value])
            <tr>
                <td width="140" valign="top" style="padding:4px 0; color:#6b7280;">{{ __($labelKey) }}</td>
                <td valign="top" style="padding:4px 0; color:#111;">{{ $value }}</td>
            </tr>
        @endforeach
    </table>

    <div style="text-align:center; margin:28px 0 8px;">
        <a href="{{ route('shop.index') }}"
            style="background:#2563eb;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;display:inline-block;">
            {{ __('client.mail.order.action') }}
        </a>
    </div>

    <p style="font-size:12px; color:#888;">{{ __('client.mail.order.outro') }}</p>
@endsection
