@extends('admin.layouts.app')

@section('title', __('admin/address.province.title'))

@section('content')
    <div class="w-full mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="mb-5">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/address.province.title') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/address.province.subtitle') }}</p>
        </div>

        <form action="{{ route('admin.provinces.index') }}" method="GET"
            class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="md:col-span-2">
                <label for="keyword"
                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.labels.keyword') }}</label>
                <input type="search" id="keyword" name="keyword" value="{{ request('keyword') }}"
                    class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="division_type"
                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/address.province.fields.division_type') }}</label>
                <select id="division_type" name="division_type"
                    class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ __('common.labels.all') }}</option>
                    @foreach (\App\Const\ProvinceConst::DIVISION_TYPE as $type)
                        <option value="{{ $type }}" @selected(request('division_type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-magnifying-glass"></i>
                    {{ __('common.actions.search') }}
                </button>
                <a href="{{ route('admin.provinces.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-[860px] w-full table-fixed admin-table">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[7%] text-center px-4 py-3">{{ __('common.labels.id') }}</th>
                        <th class="w-[27%] px-4 py-3">{{ __('admin/address.province.fields.name') }}</th>
                        <th class="w-[11%] text-center px-4 py-3">{{ __('admin/address.province.fields.code') }}</th>
                        <th class="w-[19%] px-4 py-3">{{ __('admin/address.province.fields.division_type') }}</th>
                        <th class="w-[12%] text-center px-4 py-3">{{ __('admin/address.province.fields.phone_code') }}</th>
                        <th class="w-[12%] text-center px-4 py-3">{{ __('admin/address.province.fields.wards_count') }}</th>
                        <th class="w-[12%] text-center px-4 py-3">{{ __('common.labels.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($provinces as $province)
                        <tr class="text-sm text-gray-700 transition-colors">
                            <td class="text-center px-4 py-3">{{ $province->id }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 truncate">{{ $province->name }}</td>
                            <td class="text-center px-4 py-3">{{ $province->code }}</td>
                            <td class="px-4 py-3 truncate">{{ $province->division_type }}</td>
                            <td class="text-center px-4 py-3">{{ $province->phone_code }}</td>
                            <td class="text-center px-4 py-3">{{ $province->wards_count ?? 0 }}</td>
                            <td class="text-center px-4 py-3">
                                <a href="{{ route('admin.provinces.show', $province->id) }}"
                                    class="text-blue-500 hover:text-blue-700" title="{{ __('common.actions.view') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center text-gray-500">
                                <i class="fas fa-map text-4xl text-gray-300 block mb-3"></i>
                                <p class="font-medium text-gray-700">{{ __('common.empty.title') }}</p>
                                <p class="text-sm">{{ __('common.empty.description') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $provinces->withQueryString()])
    </div>
@endsection
