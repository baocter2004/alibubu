@extends('admin.layouts.app')

@section('title', __('admin/question.title'))

@section('content')
    @include('admin.partials.page-header', [
        'title' => __('admin/question.title'),
        'subtitle' => __('admin/question.subtitle'),
        'crumbs' => [['label' => __('admin/question.title')]],
    ])

    <div class="w-full bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="flex flex-wrap items-center gap-2 mb-5">
            @foreach (['' => 'all', 'pending' => 'pending', 'answered' => 'answered', 'hidden' => 'hidden'] as $value => $key)
                <a href="{{ route('admin.questions.index', $value ? ['status' => $value] : []) }}"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request('status', '') === $value ? 'bg-primary text-white' : 'text-gray-700 bg-gray-100 hover:bg-gray-200' }}">
                    {{ __('admin/question.filters.' . $key) }}
                    @if ($key === 'pending' && $pendingCount > 0)
                        <span class="ml-1 px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-red-500 text-white">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>

        @if ($questions->isEmpty())
            <div class="py-16 text-center text-gray-500">
                <i class="fa-solid fa-comments text-4xl text-gray-300 block mb-3"></i>
                <p class="font-medium text-gray-700">{{ __('admin/question.empty') }}</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($questions as $question)
                    <div class="border border-gray-200 rounded-xl p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                            <div class="min-w-0">
                                <a href="{{ route('admin.products.show', $question->product_id) }}"
                                    class="text-sm font-semibold text-primary hover:underline">
                                    {{ $question->product?->name ?? '-' }}
                                </a>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ __('admin/question.asked_by') }}:
                                    {{ $question->user?->fullname ?: ($question->fullname ?: '-') }}
                                    · {{ $question->created_at?->format('d/m/Y H:i') }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ $question->isAnswered() ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $question->isAnswered() ? __('admin/question.answered') : __('admin/question.pending') }}
                                </span>
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ $question->is_published ? 'bg-sky-100 text-sky-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $question->is_published ? __('admin/question.published') : __('admin/question.hidden') }}
                                </span>
                            </div>
                        </div>

                        <p class="text-sm text-gray-800 bg-gray-50 border border-gray-100 rounded-lg p-3 mb-3">
                            {{ $question->question }}
                        </p>

                        <form action="{{ route('admin.questions.answer', $question->id) }}" method="POST"
                            class="space-y-3 mb-3">
                            @csrf
                            <textarea name="answer" rows="2"
                                placeholder="{{ __('admin/question.fields.answer_placeholder') }}"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-accent/30 {{ $errors->has('answer') ? 'is-invalid' : 'border-gray-300' }}">{{ old('answer', $question->answer) }}</textarea>

                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary-hover transition-colors">
                                <i class="fa-solid fa-paper-plane"></i>
                                {{ __('admin/question.answer_action') }}
                            </button>
                        </form>

                        <div class="flex flex-wrap items-center gap-2">
                            <form action="{{ route('admin.questions.toggle', $question->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <i class="fa-solid {{ $question->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                    {{ $question->is_published ? __('admin/question.hide') : __('admin/question.show') }}
                                </button>
                            </form>

                            <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST"
                                class="ml-auto" data-confirm="{{ __('common.confirm.delete_text') }}"
                                data-confirm-title="{{ __('common.confirm.delete_title') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                                    <i class="fa-regular fa-trash-can"></i>
                                    {{ __('common.actions.delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            @include('components.pagination', ['paginator' => $questions->withQueryString()])
        @endif
    </div>
@endsection
