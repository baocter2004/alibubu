@extends('admin.layouts.app')

@section('title', __('admin/order.title.show') . ' - ' . $order->code)

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ $order->code }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $order->created_at?->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.orders.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-arrow-left"></i>
                {{ __('common.actions.back') }}
            </a>
            @can('orders.mark_paid')
                @if (\App\Const\PaymentConst::isPayable($order->payment_status) && ! \App\Const\OrderConst::isVoid($order->status))
                    <form action="{{ route('admin.orders.mark-paid', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 transition-colors">
                            <i class="fas fa-money-bill-wave"></i>
                            {{ __('admin/order.payment.mark_paid') }}
                        </button>
                    </form>
                @endif
            @endcan

            @can('orders.refund')
                @if ((int) $order->payment_status === \App\Const\PaymentConst::STATUS_REFUND_PENDING)
                    <button type="button" id="mark-refunded-toggle"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-orange-500 rounded-lg hover:bg-orange-600 transition-colors">
                        <i class="fas fa-hand-holding-dollar"></i>
                        {{ __('admin/order.refund.title') }}
                    </button>
                @endif
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
                <h2 class="font-semibold text-gray-900 mb-4">{{ __('admin/order.sections.items') }}</h2>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-[560px] w-full">
                        <thead>
                            <tr class="text-xs font-semibold uppercase text-left bg-gray-50 text-gray-600">
                                <th class="px-4 py-3">{{ __('admin/order.item.product') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('admin/order.item.price') }}</th>
                                <th class="px-4 py-3 text-center">{{ __('admin/order.item.quantity') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('admin/order.item.subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <span class="block font-medium text-gray-900">{{ $item->name }}</span>
                                        @if ($item->attributes_variant)
                                            <span class="block text-xs text-gray-500">
                                                {{ implode(' / ', (array) $item->attributes_variant) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">{{ format_price($item->price) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-right font-medium whitespace-nowrap">
                                        {{ format_price($item->price * $item->quantity) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 text-sm">
                                <td colspan="3" class="px-4 py-3 text-right font-semibold text-gray-700">
                                    {{ __('admin/order.fields.total_amount') }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-primary whitespace-nowrap">
                                    {{ format_price($order->total_amount) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
                <h2 class="font-semibold text-gray-900 mb-4">{{ __('admin/order.sections.customer') }}</h2>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @php
                        $rows = [
                            ['label' => __('admin/order.fields.fullname'), 'value' => $order->fullname],
                            ['label' => __('admin/order.fields.phone_number'), 'value' => $order->phone_number],
                            ['label' => __('admin/order.fields.email'), 'value' => $order->email ?: '-'],
                            ['label' => __('admin/order.fields.customer'), 'value' => $order->user?->fullname ?? __('admin/order.item.guest')],
                        ];
                    @endphp

                    @foreach ($rows as $row)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <dt class="text-xs uppercase tracking-wide text-gray-500 mb-1">{{ $row['label'] }}</dt>
                            <dd class="text-gray-800 font-medium break-words">{{ $row['value'] }}</dd>
                        </div>
                    @endforeach

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 sm:col-span-2">
                        <dt class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                            {{ __('admin/order.fields.address') }}</dt>
                        <dd class="text-gray-800 font-medium break-words">{{ $order->address }}</dd>
                    </div>

                    @if ($order->note)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 sm:col-span-2">
                            <dt class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                                {{ __('admin/order.fields.note') }}</dt>
                            <dd class="text-gray-800 break-words">{{ $order->note }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
                <h2 class="font-semibold text-gray-900 mb-4">{{ __('admin/order.sections.actions') }}</h2>

                <div class="flex flex-wrap items-center gap-2 mb-5">
                    <span
                        class="px-3 py-1.5 text-sm font-semibold rounded-full {{ \App\Const\OrderConst::statusBadgeClass($order->status) }}">
                        {{ \App\Const\OrderConst::statusLabel($order->status) }}
                    </span>
                    <span
                        class="px-3 py-1.5 text-sm font-semibold rounded-full {{ \App\Const\PaymentConst::statusBadgeClass($order->payment_status) }}">
                        {{ \App\Const\PaymentConst::statusLabel($order->payment_status) }}
                    </span>
                </div>

                @can('orders.refund')
                    @if ((int) $order->payment_status === \App\Const\PaymentConst::STATUS_REFUND_PENDING)
                        <form action="{{ route('admin.orders.mark-refunded', $order->id) }}" method="POST"
                            id="mark-refunded-form" class="hidden mb-5 space-y-2 rounded-lg border border-orange-200 bg-orange-50/60 p-3">
                            @csrf
                            <label for="refund_note" class="block text-sm font-medium text-gray-700">
                                {{ __('admin/order.refund.note') }}
                            </label>
                            <textarea id="refund_note" name="note" rows="2"
                                class="w-full border rounded-md p-2 text-sm border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-200">{{ old('note') }}</textarea>
                            <input type="text" name="reference" placeholder="{{ __('admin/order.refund.reference') }}"
                                class="w-full border rounded-md p-2 text-sm border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-200"
                                value="{{ old('reference') }}">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors">
                                {{ __('admin/order.actions.update_status') }}
                            </button>
                        </form>
                    @endif
                @endcan

                @if (empty($transitions))
                    <p class="text-sm text-gray-500">{{ __('admin/order.actions.no_transition') }}</p>
                @else
                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST"
                        class="space-y-3" id="order-status-form">
                        @csrf
                        @method('PATCH')

                        <div class="grid gap-2" id="status-actions">
                            @foreach ($transitions as $status)
                                @if ($status === \App\Const\OrderConst::STATUS_CANCELLED)
                                    <button type="button" id="cancel-order-toggle"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors {{ \App\Const\OrderConst::statusActionClass($status) }}">
                                        <i class="fas {{ \App\Const\OrderConst::statusActionIcon($status) }}"></i>
                                        {{ __('admin/order.actions.move_to.' . $status) }}
                                    </button>
                                @else
                                    <button type="submit" name="status" value="{{ $status }}"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors {{ \App\Const\OrderConst::statusActionClass($status) }}">
                                        <i class="fas {{ \App\Const\OrderConst::statusActionIcon($status) }}"></i>
                                        {{ __('admin/order.actions.move_to.' . $status) }}
                                    </button>
                                @endif
                            @endforeach
                        </div>

                        @if (in_array(\App\Const\OrderConst::STATUS_CANCELLED, $transitions, true))
                            <div id="cancel-reason-wrap"
                                class="{{ $errors->has('cancel_reason') ? '' : 'hidden' }} rounded-lg border border-red-200 bg-red-50/60 p-3 space-y-3">
                                <div>
                                    <label for="cancel_reason" class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('admin/order.fields.cancel_reason') }}
                                    </label>
                                    <textarea id="cancel_reason" name="cancel_reason" rows="3"
                                        placeholder="{{ __('admin/order.actions.cancel_hint') }}"
                                        class="w-full border rounded-md p-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 {{ $errors->has('cancel_reason') ? 'is-invalid' : 'border-gray-300' }}">{{ old('cancel_reason') }}</textarea>
                                    @error('cancel_reason')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <button type="submit" name="status"
                                        value="{{ \App\Const\OrderConst::STATUS_CANCELLED }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                        <i class="fas fa-ban"></i>
                                        {{ __('admin/order.actions.cancel_confirm') }}
                                    </button>
                                    <button type="button" id="cancel-order-back"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                        {{ __('admin/order.actions.cancel_back') }}
                                    </button>
                                </div>
                            </div>
                        @endif

                        @error('status')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </form>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
                <h2 class="font-semibold text-gray-900 mb-4">{{ __('admin/order.sections.timeline') }}</h2>

                <ul class="space-y-3 text-sm">
                    @php
                        $timeline = [
                            ['label' => __('common.labels.created_at'), 'value' => $order->created_at, 'icon' => 'fa-cart-plus'],
                            ['label' => __('admin/order.fields.confirmed_at'), 'value' => $order->confirmed_at, 'icon' => 'fa-circle-check'],
                            ['label' => __('admin/order.fields.completed_at'), 'value' => $order->completed_at, 'icon' => 'fa-box-open'],
                            ['label' => __('admin/order.fields.cancelled_at'), 'value' => $order->cancelled_at, 'icon' => 'fa-ban'],
                        ];
                    @endphp

                    @foreach ($timeline as $entry)
                        <li class="flex items-start gap-3 {{ $entry['value'] ? '' : 'opacity-40' }}">
                            <span
                                class="w-8 h-8 shrink-0 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center">
                                <i class="fa-solid {{ $entry['icon'] }} text-xs"></i>
                            </span>
                            <span>
                                <span class="block text-gray-700 font-medium">{{ $entry['label'] }}</span>
                                <span
                                    class="block text-xs text-gray-500">{{ $entry['value']?->format('d/m/Y H:i') ?? '-' }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>

                @if ($order->cancel_reason)
                    <div class="mt-4 p-3 bg-red-50 border border-red-100 rounded-lg">
                        <p class="text-xs uppercase tracking-wide text-red-500 mb-1">
                            {{ __('admin/order.fields.cancel_reason') }}</p>
                        <p class="text-sm text-red-700">{{ $order->cancel_reason }}</p>
                    </div>
                @endif
            </div>

            @if ($order->paymentTransactions->isNotEmpty())
                <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
                    <h2 class="font-semibold text-gray-900 mb-4">{{ __('admin/order.fields.payment') }}</h2>

                    <ul class="space-y-2 text-sm">
                        @foreach ($order->paymentTransactions as $transaction)
                            <li class="flex items-center justify-between gap-2 border-b border-gray-100 pb-2 last:border-0 last:pb-0">
                                <span class="text-gray-600">
                                    {{ \App\Const\PaymentConst::gatewayLabel($transaction->gateway) }} ·
                                    {{ $transaction->created_at?->format('d/m/Y H:i') }}
                                </span>
                                <span
                                    class="font-medium {{ $transaction->is_successful ? 'text-green-600' : 'text-red-500' }}">
                                    {{ format_price($transaction->amount) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            function showCancelPanel(show) {
                $('#cancel-reason-wrap').toggleClass('hidden', !show);
                $('#status-actions').toggleClass('hidden', show);

                if (show) {
                    $('#cancel_reason').trigger('focus');
                }
            }

            $('#cancel-order-toggle').on('click', function() {
                showCancelPanel(true);
            });

            $('#cancel-order-back').on('click', function() {
                showCancelPanel(false);
            });

            if (!$('#cancel-reason-wrap').hasClass('hidden')) {
                showCancelPanel(true);
            }

            $('#mark-refunded-toggle').on('click', function() {
                $('#mark-refunded-form').toggleClass('hidden');
            });
        });
    </script>
@endpush
