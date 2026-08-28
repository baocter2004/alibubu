@extends('admin.layouts.app')

@section('title', __('admin/branch.title.show'))

@section('content')
    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ $branch->name }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/branch.subtitle.show') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.branches.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('common.actions.back') }}
                </a>
                <a href="{{ route('admin.branches.edit', $branch->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 transition-colors">
                    <i class="fas fa-edit"></i>
                    {{ __('common.actions.edit') }}
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div>
                <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center">
                    @if ($branch->logo)
                        <img src="{{ Storage::disk('public')->url($branch->logo) }}" alt="{{ $branch->name }}"
                            class="w-full h-full object-contain p-6">
                    @else
                        <i class="fa-solid fa-store text-6xl text-gray-300"></i>
                    @endif
                </div>
            </div>

            <dl class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4 content-start">
                @php
                    $rows = [
                        ['label' => __('common.labels.id'), 'value' => $branch->id],
                        ['label' => __('admin/branch.fields.name'), 'value' => $branch->name],
                        ['label' => __('admin/branch.fields.slug'), 'value' => $branch->slug],
                        ['label' => __('admin/branch.fields.products_count'), 'value' => $branch->products_count ?? $branch->products()->count()],
                        ['label' => __('common.labels.status'), 'value' => \App\Const\GlobalConst::statusLabel($branch->is_active)],
                        ['label' => __('common.labels.created_at'), 'value' => $branch->created_at?->format('d/m/Y H:i')],
                        ['label' => __('common.labels.updated_at'), 'value' => $branch->updated_at?->format('d/m/Y H:i')],
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
    </div>
@endsection
