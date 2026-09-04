@extends('admin.layouts.app')

@section('title', __('admin/user.title.confirm'))

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
            <div class="mb-6">
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/user.title.confirm') }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/user.subtitle.confirm') }}</p>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $rows = [
                        ['label' => __('admin/user.fields.fullname'), 'value' => $data['fullname'] ?? '-'],
                        ['label' => __('admin/user.fields.email'), 'value' => $data['email'] ?? '-'],
                        ['label' => __('admin/user.fields.phone_number'), 'value' => $data['phone_number'] ?? '-'],
                        ['label' => __('admin/user.fields.gender'), 'value' => \App\Const\UserConst::genderLabel($data['gender'] ?? null)],
                        ['label' => __('admin/user.fields.birthday'), 'value' => ! empty($data['birthday']) ? \Illuminate\Support\Carbon::parse($data['birthday'])->format('d/m/Y') : '-'],
                        ['label' => __('admin/user.fields.role'), 'value' => \App\Const\UserConst::roleLabel($data['role'] ?? null)],
                        ['label' => __('admin/user.fields.status'), 'value' => \App\Const\UserConst::statusLabel($data['status'] ?? null)],
                        ['label' => __('admin/user.fields.bank_name'), 'value' => \App\Const\BankConst::getOptions()[$data['bank_name'] ?? ''] ?? '-'],
                        ['label' => __('admin/user.fields.user_bank_name'), 'value' => $data['user_bank_name'] ?? '-'],
                        ['label' => __('admin/user.fields.bank_account'), 'value' => $data['bank_account'] ?? '-'],
                    ];
                @endphp

                @foreach ($rows as $row)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <dt class="text-xs uppercase tracking-wide text-gray-500 mb-1">{{ $row['label'] }}</dt>
                        <dd class="text-gray-800 font-medium break-words">{{ $row['value'] ?: '-' }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
            <h2 class="font-semibold text-gray-900 mb-4">{{ __('admin/user.address.section') }}</h2>

            @if (empty($data['user_addresses']))
                <p class="py-8 text-center text-sm text-gray-500">{{ __('admin/user.address.empty') }}</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($data['user_addresses'] as $address)
                        <div
                            class="border rounded-xl p-4 {{ ! empty($address['is_default']) ? 'border-primary/30 bg-primary-soft/50' : 'border-gray-200 bg-gray-50' }}">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <p class="font-medium text-gray-900">{{ $address['fullname'] ?? '-' }}</p>
                                @if (! empty($address['is_default']))
                                    <span
                                        class="px-2 py-0.5 text-[11px] font-semibold rounded-full bg-primary text-white shrink-0">
                                        {{ __('admin/user.address.default_badge') }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 mb-1">
                                <i class="fa-solid fa-phone text-gray-400 mr-1.5"></i>{{ $address['phone_number'] ?? '-' }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <i class="fa-solid fa-location-dot text-gray-400 mr-1.5"></i>
                                {{ collect([$address['address'] ?? null, $address['ward'] ?? null, $address['province'] ?? null])->filter()->implode(', ') ?: '-' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="flex flex-col sm:flex-row justify-end gap-3">
            <a href="{{ !empty($data['id']) ? route('admin.users.edit', $data['id']) : route('admin.users.create') }}"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('common.actions.back') }}
            </a>

            <form action="{{ route('admin.users.save') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-green-500 rounded-lg hover:bg-green-600 transition-colors">
                    <i class="fa-solid fa-floppy-disk"></i>
                    {{ __('common.actions.save') }}
                </button>
            </form>
        </div>
    </div>
@endsection
