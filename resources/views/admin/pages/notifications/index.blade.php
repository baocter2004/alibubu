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
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.notifications.index') }}"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors {{ $filter === \App\Const\NotificationConst::FILTER_ALL ? 'bg-primary text-white' : 'text-gray-700 bg-gray-100 hover:bg-gray-200' }}">
                    {{ __('admin/notification.filters.all') }} ({{ $counts['all'] }})
                </a>
                <a href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors {{ $filter === \App\Const\NotificationConst::FILTER_UNREAD ? 'bg-primary text-white' : 'text-gray-700 bg-gray-100 hover:bg-gray-200' }}">
                    {{ __('admin/notification.filters.unread') }} ({{ $counts['unread'] }})
                </a>
            </div>

            <div class="flex items-center gap-2">
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

                @if ($counts['read'] > 0)
                    <form action="{{ route('admin.notifications.destroy-read') }}" method="POST"
                        data-confirm="{{ __('admin/notification.delete_read_confirm') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                            <i class="fas fa-trash"></i>
                            {{ __('admin/notification.delete_read') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if ($notifications->isEmpty())
            <div class="py-16 text-center text-gray-500">
                <i class="fas fa-bell-slash text-4xl text-gray-300 block mb-3"></i>
                <p class="font-medium text-gray-700">{{ __('admin/notification.empty') }}</p>
            </div>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach ($notifications as $notification)
                    <li class="flex flex-wrap items-start gap-3 py-4 {{ $notification['is_read'] ? '' : 'bg-primary-soft/40' }}">
                        <span
                            class="w-10 h-10 shrink-0 rounded-lg flex items-center justify-center {{ \App\Const\NotificationConst::levelIconClass($notification['level'], $notification['is_read']) }}">
                            <i class="fas {{ $notification['icon'] }}"></i>
                        </span>

                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900">
                                {{ $notification['title'] }}
                                @unless ($notification['is_read'])
                                    <span
                                        class="ml-1 px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-primary text-white align-middle">
                                        {{ __('admin/notification.unread') }}
                                    </span>
                                @endunless
                            </p>
                            @if ($notification['body'])
                                <p class="text-sm text-gray-600 mt-0.5">{{ $notification['body'] }}</p>
                            @endif
                            @if ($notification['reason'])
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ __('admin/notification.reason', ['reason' => $notification['reason']]) }}
                                </p>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">{{ $notification['datetime'] }}</p>
                        </div>

                        @if ($notification['url'])
                            <form action="{{ route('admin.notifications.read', $notification['id']) }}" method="POST"
                                class="shrink-0">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-primary border border-primary/20 rounded-lg hover:bg-primary-soft transition-colors">
                                    <i class="fas fa-arrow-right"></i>
                                    {{ __('common.actions.view_detail') }}
                                </button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>

            @include('components.pagination', ['paginator' => $notifications->withQueryString()])
        @endif
    </div>
@endsection
