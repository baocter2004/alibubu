@extends('admin.layouts.app')

@section('title', __('admin/notification.title'))

@section('content')
    @include('admin.partials.page-header', [
        'title' => __('admin/notification.title'),
        'subtitle' => __('admin/notification.subtitle'),
        'crumbs' => [['label' => __('admin/notification.title')]],
    ])

    <div class="w-full bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <p class="text-sm text-gray-600">
                {{ __('admin/notification.unread') }}:
                <span class="font-semibold text-primary tabular">{{ $unreadCount }}</span>
            </p>

            @if ($unreadCount > 0)
                <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-check-double"></i>
                        {{ __('admin/notification.mark_all_read') }}
                    </button>
                </form>
            @endif
        </div>

        @if ($notifications->isEmpty())
            <div class="py-16 text-center text-gray-500">
                <i class="fas fa-bell-slash text-4xl text-gray-300 block mb-3"></i>
                <p class="font-medium text-gray-700">{{ __('admin/notification.empty') }}</p>
            </div>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach ($notifications as $notification)
                    @php $data = $notification->data; @endphp
                    <li class="flex flex-wrap items-start gap-3 py-4 {{ $notification->read_at ? '' : 'bg-primary-soft/40' }}">
                        @php $isQuestion = ($data['type'] ?? null) === 'product.question'; @endphp

                        <span
                            class="w-10 h-10 shrink-0 rounded-lg flex items-center justify-center {{ $notification->read_at ? 'bg-gray-100 text-gray-400' : 'bg-primary/10 text-primary' }}">
                            <i class="fas {{ $isQuestion ? 'fa-comments' : 'fa-receipt' }}"></i>
                        </span>

                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900">
                                {{ $isQuestion
                                    ? __('admin/notification.question.asked', ['product' => $data['product_name'] ?? '-'])
                                    : __('admin/notification.order.placed', ['code' => $data['order_code'] ?? '-']) }}
                                @unless ($notification->read_at)
                                    <span
                                        class="ml-1 px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-primary text-white align-middle">
                                        {{ __('admin/notification.unread') }}
                                    </span>
                                @endunless
                            </p>
                            <p class="text-sm text-gray-600 mt-0.5">
                                @if ($isQuestion)
                                    {{ Str::limit($data['question'] ?? '', 120) }}
                                @else
                                {{ __('admin/notification.order.detail', [
                                    'customer' => $data['customer'] ?? '-',
                                    'items' => $data['items_count'] ?? 0,
                                    'total' => format_price($data['total_amount'] ?? 0),
                                ]) }}
                                @endif
                            </p>
                            <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at?->format('d/m/Y H:i') }}</p>
                        </div>

                        <form action="{{ route('admin.notifications.read', $notification->id) }}" method="POST"
                            class="shrink-0">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-primary border border-primary/20 rounded-lg hover:bg-primary-soft transition-colors">
                                <i class="fas fa-arrow-right"></i>
                                {{ __('common.actions.view_detail') }}
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>

            @include('components.pagination', ['paginator' => $notifications->withQueryString()])
        @endif
    </div>
@endsection
