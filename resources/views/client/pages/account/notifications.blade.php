@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client.notifications.title'))

@section('content')
    <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-x-2 text-sm text-muted-foreground mb-6">
        <a href="{{ route('index') }}" class="inline-flex items-center min-h-7 py-0.5 hover:text-primary transition-colors">{{ __('client.nav.home') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-foreground font-medium">{{ __('client.notifications.title') }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6 items-start">
        @include('client.pages.account.nav')

        <div class="flex-1 min-w-0">
            <section class="bg-card border border-border rounded-2xl p-5 md:p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                    <div>
                        <h1 class="text-lg font-bold text-foreground">{{ __('client.notifications.title') }}</h1>
                        <p class="text-sm text-muted-foreground mt-0.5">{{ __('client.notifications.subtitle') }}</p>
                    </div>

                    @if ($counts['unread'] > 0)
                        <form action="{{ route('account.notifications.read-all') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-foreground border border-border rounded-lg hover:bg-muted transition-colors">
                                <i class="fa-solid fa-check-double"></i>
                                {{ __('client.notifications.mark_all_read') }}
                            </button>
                        </form>
                    @endif
                </div>

                <div class="flex items-center gap-2 mb-5">
                    <a href="{{ route('account.notifications.index') }}"
                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors {{ $filter === \App\Const\NotificationConst::FILTER_ALL ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted' }}">
                        {{ __('client.notifications.filters.all') }} ({{ $counts['all'] }})
                    </a>
                    <a href="{{ route('account.notifications.index', ['filter' => 'unread']) }}"
                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors {{ $filter === \App\Const\NotificationConst::FILTER_UNREAD ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted' }}">
                        {{ __('client.notifications.filters.unread') }} ({{ $counts['unread'] }})
                    </a>
                </div>

                @if ($notifications->isEmpty())
                    <div class="py-16 text-center">
                        <i class="fa-regular fa-bell-slash text-5xl text-muted-foreground/25 mb-4"></i>
                        <p class="text-foreground font-medium">
                            {{ $filter === \App\Const\NotificationConst::FILTER_UNREAD
                                ? __('client.notifications.empty_unread')
                                : __('client.notifications.empty') }}
                        </p>
                    </div>
                @else
                    <ul class="divide-y divide-border">
                        @foreach ($notifications as $notification)
                            <li class="flex flex-wrap items-start gap-3 py-4 {{ $notification['is_read'] ? '' : 'bg-primary/5' }}">
                                <span
                                    class="w-10 h-10 shrink-0 rounded-lg flex items-center justify-center {{ \App\Const\NotificationConst::levelIconClass($notification['level'], $notification['is_read']) }}">
                                    <i class="fa-solid {{ $notification['icon'] }}"></i>
                                </span>

                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-foreground">
                                        {{ $notification['title'] }}
                                        @unless ($notification['is_read'])
                                            <span
                                                class="ml-1 px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-primary text-white align-middle">
                                                {{ __('client.notifications.unread') }}
                                            </span>
                                        @endunless
                                    </p>
                                    @if ($notification['body'])
                                        <p class="text-sm text-muted-foreground mt-0.5">{{ $notification['body'] }}</p>
                                    @endif
                                    @if ($notification['reason'])
                                        <p class="text-xs text-muted-foreground mt-1">
                                            {{ __('client.notifications.reason', ['reason' => $notification['reason']]) }}
                                        </p>
                                    @endif
                                    <p class="text-xs text-muted-foreground/70 mt-1">{{ $notification['datetime'] }}</p>
                                </div>

                                @if ($notification['url'])
                                    <form action="{{ route('account.notifications.read', $notification['id']) }}" method="POST"
                                        class="shrink-0">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-primary border border-primary/20 rounded-lg hover:bg-primary/5 transition-colors">
                                            <i class="fa-solid fa-arrow-right"></i>
                                            {{ __('client.notifications.view') }}
                                        </button>
                                    </form>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    @include('components.pagination', ['paginator' => $notifications->withQueryString()])
                @endif
            </section>
        </div>
    </div>
@endsection
