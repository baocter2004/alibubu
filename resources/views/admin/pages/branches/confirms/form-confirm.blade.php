@extends('admin.layouts.app')

@section('title', __('admin/branch.title.confirm'))

@section('content')
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="mb-6">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/branch.title.confirm') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/branch.subtitle.confirm') }}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center">
                @if (! empty($data['logo']))
                    <img src="{{ Storage::disk('public')->url($data['logo']) }}" alt="{{ $data['name'] ?? '' }}"
                        class="w-full h-full object-contain p-4">
                @else
                    <i class="fa-solid fa-store text-5xl text-gray-300"></i>
                @endif
            </div>

            <dl class="sm:col-span-2 grid grid-cols-1 gap-4 content-start">
                @php
                    $rows = [
                        ['label' => __('admin/branch.fields.name'), 'value' => $data['name'] ?? '-'],
                        ['label' => __('admin/branch.fields.slug'), 'value' => $data['slug'] ?? '-'],
                        ['label' => __('common.labels.status'), 'value' => \App\Const\GlobalConst::statusLabel($data['is_active'] ?? null)],
                    ];
                @endphp

                @foreach ($rows as $row)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <dt class="text-xs uppercase tracking-wide text-gray-500 mb-1">{{ $row['label'] }}</dt>
                        <dd class="text-gray-800 font-medium break-words">{{ $row['value'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ !empty($data['id']) ? route('admin.branches.edit', $data['id']) : route('admin.branches.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('common.actions.back') }}
            </a>

            <form action="{{ route('admin.branches.save') }}" method="POST">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-green-500 rounded-lg hover:bg-green-600 transition-colors">
                    <i class="fa-solid fa-floppy-disk"></i>
                    {{ __('common.actions.save') }}
                </button>
            </form>
        </div>
    </div>
@endsection
