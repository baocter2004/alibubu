@extends('admin.layouts.app')

@section('title', __('admin/attribute.title.show'))

@section('content')
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ $attribute->name }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $attribute->slug }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.attributes.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('common.actions.back') }}
                </a>
                <a href="{{ route('admin.attributes.edit', $attribute->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 transition-colors">
                    <i class="fas fa-edit"></i>
                    {{ __('common.actions.edit') }}
                </a>
            </div>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            @foreach ([['label' => __('common.labels.status'), 'value' => \App\Const\GlobalConst::statusLabel($attribute->is_active)], ['label' => __('admin/attribute.fields.values_count'), 'value' => $attribute->values->count()], ['label' => __('common.labels.created_at'), 'value' => $attribute->created_at?->format('d/m/Y H:i')]] as $row)
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <dt class="text-xs uppercase tracking-wide text-gray-500 mb-1">{{ $row['label'] }}</dt>
                    <dd class="text-gray-800 font-medium">{{ $row['value'] }}</dd>
                </div>
            @endforeach
        </dl>

        <h2 class="font-semibold text-gray-900 mb-3">{{ __('admin/attribute.value_section.title') }}</h2>

        @if ($attribute->values->isEmpty())
            <p class="py-8 text-center text-sm text-gray-500">{{ __('admin/attribute.value_section.empty') }}</p>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach ($attribute->values as $value)
                    <span
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm rounded-lg border {{ $value->is_active ? 'border-primary/20 bg-primary-soft text-primary' : 'border-gray-200 bg-gray-50 text-gray-500' }}">
                        {{ $value->value }}
                        @unless ($value->is_active)
                            <i class="fa-solid fa-eye-slash text-xs"></i>
                        @endunless
                    </span>
                @endforeach
            </div>
        @endif
    </div>
@endsection
