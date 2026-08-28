@extends('admin.layouts.app')

@section('title', __('admin/address.province.show'))

@section('content')
    <div class="w-full mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ $province->name }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $province->division_type }}</p>
            </div>
            <a href="{{ route('admin.provinces.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-arrow-left"></i>
                {{ __('common.actions.back') }}
            </a>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
                $rows = [
                    ['label' => __('admin/address.province.fields.name'), 'value' => $province->name],
                    ['label' => __('admin/address.province.fields.code'), 'value' => $province->code],
                    ['label' => __('admin/address.province.fields.codename'), 'value' => $province->codename],
                    ['label' => __('admin/address.province.fields.division_type'), 'value' => $province->division_type],
                    ['label' => __('admin/address.province.fields.phone_code'), 'value' => $province->phone_code],
                    ['label' => __('common.labels.created_at'), 'value' => $province->created_at?->format('d/m/Y H:i')],
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

    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <h2 class="font-semibold text-gray-900 mb-4">{{ __('admin/address.ward.title') }}</h2>

        <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-[640px] w-full table-fixed">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[10%] text-center px-4 py-3">{{ __('common.labels.id') }}</th>
                        <th class="w-[38%] px-4 py-3">{{ __('admin/address.ward.fields.name') }}</th>
                        <th class="w-[16%] text-center px-4 py-3">{{ __('admin/address.ward.fields.code') }}</th>
                        <th class="w-[24%] px-4 py-3">{{ __('admin/address.ward.fields.division_type') }}</th>
                        <th class="w-[12%] text-center px-4 py-3">{{ __('common.labels.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($wards as $ward)
                        <tr class="text-sm text-gray-700 hover:bg-blue-50 transition-colors">
                            <td class="text-center px-4 py-3">{{ $ward->id }}</td>
                            <td class="px-4 py-3 truncate">{{ $ward->name }}</td>
                            <td class="text-center px-4 py-3">{{ $ward->code }}</td>
                            <td class="px-4 py-3 truncate">{{ $ward->division_type }}</td>
                            <td class="text-center px-4 py-3">
                                <a href="{{ route('admin.wards.show', $ward->id) }}"
                                    class="text-blue-500 hover:text-blue-700" title="{{ __('common.actions.view') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                                {{ __('common.empty.title') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $wards])
    </div>
@endsection
