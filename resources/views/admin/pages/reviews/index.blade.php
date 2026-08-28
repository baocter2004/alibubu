@extends('admin.layouts.app')

@section('title', __('admin/review.title'))

@section('content')
    <div class="mb-6">
        <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/review.title') }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/review.subtitle') }}</p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6">
        @foreach ([['key' => 'pending', 'value' => $pending, 'icon' => 'fa-clock', 'tone' => 'bg-amber-50 text-amber-600', 'filter' => 'pending'], ['key' => 'approved', 'value' => $approved, 'icon' => 'fa-circle-check', 'tone' => 'bg-green-50 text-green-600', 'filter' => 'approved']] as $card)
            <a href="{{ route('admin.reviews.index', ['status' => $card['filter']]) }}"
                class="bg-white rounded-xl border p-4 transition-all hover:shadow-md {{ request('status') === $card['filter'] ? 'border-blue-400 ring-1 ring-blue-200' : 'border-gray-100' }}">
                <span class="w-9 h-9 rounded-lg flex items-center justify-center mb-2 {{ $card['tone'] }}">
                    <i class="fa-solid {{ $card['icon'] }} text-sm"></i>
                </span>
                <span class="block text-xs text-gray-500">{{ __('admin/review.stats.' . $card['key']) }}</span>
                <span class="block text-xl font-bold text-gray-900">{{ number_format($card['value']) }}</span>
            </a>
        @endforeach

        <a href="{{ route('admin.reviews.index') }}"
            class="bg-white rounded-xl border p-4 flex flex-col justify-center transition-all hover:shadow-md {{ request('status') ? 'border-gray-100' : 'border-blue-400 ring-1 ring-blue-200' }}">
            <span class="text-sm font-medium text-gray-700">{{ __('admin/review.status.all') }}</span>
            <span class="text-xs text-gray-500 mt-1">{{ number_format($pending + $approved) }}</span>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <form action="{{ route('admin.reviews.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 mb-5">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="search" name="keyword" value="{{ request('keyword') }}"
                placeholder="{{ __('common.labels.keyword') }}"
                class="flex-1 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit"
                class="px-5 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                <i class="fas fa-magnifying-glass mr-1"></i>{{ __('common.actions.search') }}
            </button>
        </form>

        <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-[900px] w-full table-fixed admin-table">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[22%] px-4 py-3">{{ __('admin/review.fields.product') }}</th>
                        <th class="w-[16%] px-4 py-3">{{ __('admin/review.fields.customer') }}</th>
                        <th class="w-[12%] text-center px-4 py-3">{{ __('admin/review.fields.rating') }}</th>
                        <th class="w-[26%] px-4 py-3">{{ __('admin/review.fields.review') }}</th>
                        <th class="w-[12%] text-center px-4 py-3">{{ __('admin/review.fields.status') }}</th>
                        <th class="w-[12%] text-center px-4 py-3">{{ __('common.labels.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($reviews as $review)
                        <tr class="text-sm text-gray-700 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.products.show', $review->product_id) }}"
                                    class="font-medium text-blue-600 hover:underline line-clamp-2">
                                    {{ $review->product?->name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 truncate">
                                {{ $review->user?->fullname }}
                                <span class="block text-xs text-gray-500 truncate">{{ $review->created_at?->format('d/m/Y') }}</span>
                            </td>
                            <td class="text-center px-4 py-3">
                                @include('components.rating', ['rating' => $review->rating, 'showValue' => false])
                            </td>
                            <td class="px-4 py-3">
                                @if ($review->title)
                                    <span class="block font-medium text-gray-900 truncate">{{ $review->title }}</span>
                                @endif
                                <span class="block text-xs text-gray-500 line-clamp-2">{{ $review->comment }}</span>
                            </td>
                            <td class="text-center px-4 py-3">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ $review->is_approved ? 'text-green-600 bg-green-100' : 'text-amber-600 bg-amber-100' }}">
                                    {{ $review->is_approved ? __('admin/review.status.approved') : __('admin/review.status.pending') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-3">
                                    @if ($review->is_approved)
                                        <form action="{{ route('admin.reviews.reject', $review->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-amber-500 hover:text-amber-700"
                                                title="{{ __('admin/review.actions.reject') }}">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800"
                                                title="{{ __('admin/review.actions.approve') }}">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST"
                                        data-confirm="{{ __('common.confirm.force_delete_text') }}"
                                        data-confirm-title="{{ __('common.confirm.force_delete_title') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700"
                                            title="{{ __('common.actions.delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center text-gray-500">
                                <i class="fas fa-star text-4xl text-gray-300 block mb-3"></i>
                                <p class="font-medium text-gray-700">{{ __('common.empty.title') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $reviews->withQueryString()])
    </div>
@endsection
