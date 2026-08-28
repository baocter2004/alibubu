@extends('admin.layouts.app')

@section('title', __('admin/attribute.title.confirm'))

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="mb-6">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/attribute.title.confirm') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/attribute.subtitle.confirm') }}</p>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            @foreach ([['label' => __('admin/attribute.fields.name'), 'value' => $data['name'] ?? '-'], ['label' => __('admin/attribute.fields.slug'), 'value' => $data['slug'] ?? '-'], ['label' => __('common.labels.status'), 'value' => \App\Const\GlobalConst::statusLabel($data['is_active'] ?? null)]] as $row)
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <dt class="text-xs uppercase tracking-wide text-gray-500 mb-1">{{ $row['label'] }}</dt>
                    <dd class="text-gray-800 font-medium break-words">{{ $row['value'] }}</dd>
                </div>
            @endforeach
        </dl>

        <h2 class="font-semibold text-gray-900 mb-3">{{ __('admin/attribute.value_section.title') }}</h2>

        <div class="flex flex-wrap gap-2 mb-8">
            @forelse ($data['values'] ?? [] as $value)
                <span
                    class="inline-flex items-center gap-2 px-3 py-1.5 text-sm rounded-lg border {{ !empty($value['is_active']) ? 'border-blue-200 bg-blue-50 text-blue-700' : 'border-gray-200 bg-gray-50 text-gray-500' }}">
                    {{ $value['value'] }}
                </span>
            @empty
                <p class="text-sm text-gray-500">{{ __('admin/attribute.value_section.empty') }}</p>
            @endforelse
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ !empty($data['id']) ? route('admin.attributes.edit', $data['id']) : route('admin.attributes.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('common.actions.back') }}
            </a>

            <form action="{{ route('admin.attributes.save') }}" method="POST">
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
