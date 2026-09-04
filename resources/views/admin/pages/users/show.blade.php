@extends('admin.layouts.app')

@section('title', __('admin/user.title.show'))

@section('content')
    <div class="w-full mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div class="flex items-center gap-4">
                <span
                    class="w-14 h-14 shrink-0 rounded-full bg-primary-soft text-primary text-xl font-bold flex items-center justify-center">
                    {{ Str::upper(Str::substr($user->fullname, 0, 1)) }}
                </span>
                <div>
                    <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ $user->fullname }}</h1>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('common.actions.back') }}
                </a>
                <a href="{{ route('admin.users.edit', $user->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 transition-colors">
                    <i class="fas fa-edit"></i>
                    {{ __('common.actions.edit') }}
                </a>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 mb-6">
            <span
                class="px-3 py-1.5 text-xs font-semibold rounded-full {{ \App\Const\UserConst::statusBadgeClass($user->status) }}">
                {{ \App\Const\UserConst::statusLabel($user->status) }}
            </span>
            <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-primary-soft text-primary">
                {{ \App\Const\UserConst::roleLabel($user->role) }}
            </span>
            <span
                class="px-3 py-1.5 text-xs font-semibold rounded-full {{ $user->email_verified_at ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }}">
                <i class="fa-solid {{ $user->email_verified_at ? 'fa-circle-check' : 'fa-circle-xmark' }} mr-1"></i>
                {{ __('admin/user.fields.email_verified_at') }}
            </span>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
                $rows = [
                    ['label' => __('common.labels.id'), 'value' => $user->id],
                    ['label' => __('admin/user.fields.fullname'), 'value' => $user->fullname],
                    ['label' => __('admin/user.fields.email'), 'value' => $user->email],
                    ['label' => __('admin/user.fields.phone_number'), 'value' => $user->phone_number ?: '-'],
                    ['label' => __('admin/user.fields.gender'), 'value' => \App\Const\UserConst::genderLabel($user->gender)],
                    ['label' => __('admin/user.fields.birthday'), 'value' => $user->birthday?->format('d/m/Y') ?? '-'],
                    ['label' => __('admin/user.fields.loyalty_points'), 'value' => number_format($user->loyalty_points ?? 0)],
                    ['label' => __('admin/user.fields.bank_name'), 'value' => \App\Const\BankConst::getOptions()[$user->bank_name] ?? '-'],
                    ['label' => __('admin/user.fields.user_bank_name'), 'value' => $user->user_bank_name ?: '-'],
                    ['label' => __('admin/user.fields.bank_account'), 'value' => $user->bank_account ?: '-'],
                    ['label' => __('common.labels.created_at'), 'value' => $user->created_at?->format('d/m/Y H:i')],
                    ['label' => __('common.labels.updated_at'), 'value' => $user->updated_at?->format('d/m/Y H:i')],
                ];
            @endphp

            @foreach ($rows as $row)
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <dt class="text-xs uppercase tracking-wide text-gray-500 mb-1">{{ $row['label'] }}</dt>
                    <dd class="text-gray-800 font-medium break-words">{{ $row['value'] }}</dd>
                </div>
            @endforeach
        </dl>

        @if ($user->reason_lock)
            <div class="mt-5 p-4 bg-red-50 border border-red-100 rounded-lg">
                <p class="text-xs uppercase tracking-wide text-red-500 mb-1">
                    {{ __('admin/user.fields.reason_lock') }}</p>
                <p class="text-sm text-red-700">{{ $user->reason_lock }}</p>
            </div>
        @endif
    </div>

    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <h2 class="font-semibold text-gray-900 mb-4">{{ __('admin/user.address.section') }}</h2>

        @if ($user->userAddresses->isEmpty())
            <p class="py-10 text-center text-sm text-gray-500">{{ __('admin/user.address.empty') }}</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($user->userAddresses as $address)
                    <div
                        class="border rounded-xl p-4 {{ $address->is_default ? 'border-primary/30 bg-primary-soft/50' : 'border-gray-200 bg-gray-50' }}">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <p class="font-medium text-gray-900">{{ $address->fullname }}</p>
                            @if ($address->is_default)
                                <span
                                    class="px-2 py-0.5 text-[11px] font-semibold rounded-full bg-primary text-white shrink-0">
                                    {{ __('admin/user.address.default_badge') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mb-1">
                            <i class="fa-solid fa-phone text-gray-400 mr-1.5"></i>{{ $address->phone_number }}
                        </p>
                        <p class="text-sm text-gray-600">
                            <i class="fa-solid fa-location-dot text-gray-400 mr-1.5"></i>{{ $address->full_address }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
