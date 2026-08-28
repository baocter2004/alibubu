@extends('admin.layouts.app')

@section('title', __('admin/address.ward.show'))

@section('content')
    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ $ward->name }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $ward->province?->name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.wards.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('common.actions.back') }}
                </a>
                @if ($ward->province)
                    <a href="{{ route('admin.provinces.show', $ward->province_id) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <i class="fas fa-map"></i>
                        {{ __('admin/address.ward.fields.province') }}
                    </a>
                @endif
            </div>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
                $rows = [
                    ['label' => __('common.labels.id'), 'value' => $ward->id],
                    ['label' => __('admin/address.ward.fields.name'), 'value' => $ward->name],
                    ['label' => __('admin/address.ward.fields.code'), 'value' => $ward->code],
                    ['label' => __('admin/address.ward.fields.codename'), 'value' => $ward->codename],
                    ['label' => __('admin/address.ward.fields.division_type'), 'value' => $ward->division_type],
                    ['label' => __('admin/address.ward.fields.province'), 'value' => $ward->province?->name ?? '-'],
                    ['label' => __('common.labels.created_at'), 'value' => $ward->created_at?->format('d/m/Y H:i')],
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
@endsection
