@php
    $maxAddresses = 5;
    $currentAddresses = ! empty($user)
        ? $user->userAddresses->toArray()
        : (old('user_addresses') ?? ($data['user_addresses'] ?? [[]]));

    $hasDefault = false;
    foreach ($currentAddresses as &$addr) {
        $addr['is_default'] = ! $hasDefault && ! empty($addr['is_default']);
        $hasDefault = $hasDefault || $addr['is_default'];
    }
    unset($addr);
@endphp

<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf

    <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <header class="flex items-center gap-3 px-5 py-4 border-b border-gray-100 bg-gray-50/60">
            <span class="w-8 h-8 shrink-0 flex items-center justify-center rounded-full bg-blue-500 text-white text-sm font-semibold">1</span>
            <span>
                <span class="block font-semibold text-gray-900">{{ __('admin/user.sections.basic') }}</span>
                <span class="block text-sm text-gray-500">{{ __('admin/user.sections.basic_hint') }}</span>
            </span>
        </header>

        <div class="p-5 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @include('components.input', [
                    'name' => 'fullname',
                    'required' => true,
                    'label' => __('admin/user.fields.fullname'),
                    'value' => $user->fullname ?? ($data['fullname'] ?? ''),
                    'icon' => 'user-tag',
                ])

                @include('components.input', [
                    'name' => 'email',
                    'required' => true,
                    'label' => __('admin/user.fields.email'),
                    'value' => $user->email ?? ($data['email'] ?? ''),
                    'icon' => 'envelope',
                ])
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @include('components.input', [
                    'name' => 'phone_number',
                    'required' => true,
                    'label' => __('admin/user.fields.phone_number'),
                    'value' => $user->phone_number ?? ($data['phone_number'] ?? ''),
                    'icon' => 'phone',
                ])
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @include('components.input', [
                    'name' => 'password',
                    'label' => __('admin/user.fields.password'),
                    'type' => 'password',
                    'required' => empty($user->id),
                    'icon' => 'lock',
                ])

                @include('components.input', [
                    'name' => 'password_confirmation',
                    'label' => __('admin/user.fields.password_confirmation'),
                    'type' => 'password',
                    'required' => empty($user->id),
                    'icon' => 'lock',
                ])
            </div>

            @if (! empty($user))
                <p class="flex items-start gap-2 text-sm text-gray-600 bg-amber-50 border border-amber-100 rounded-lg p-3">
                    <i class="fa-solid fa-circle-info text-amber-500 mt-0.5"></i>
                    {{ __('admin/user.hints.password_optional') }}
                </p>
            @endif
        </div>
    </section>

    <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <header class="flex items-center gap-3 px-5 py-4 border-b border-gray-100 bg-gray-50/60">
            <span class="w-8 h-8 shrink-0 flex items-center justify-center rounded-full bg-blue-500 text-white text-sm font-semibold">2</span>
            <span>
                <span class="block font-semibold text-gray-900">{{ __('admin/user.sections.personal') }}</span>
                <span class="block text-sm text-gray-500">{{ __('admin/user.sections.personal_hint') }}</span>
            </span>
        </header>

        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
            @include('components.select', [
                'name' => 'gender',
                'label' => __('admin/user.fields.gender'),
                'icon' => 'venus-mars',
                'placeholder' => __('common.labels.none'),
                'value' => $user->gender ?? ($data['gender'] ?? ''),
                'options' => \App\Const\UserConst::genders(),
            ])

            @include('components.date', [
                'name' => 'birthday',
                'label' => __('admin/user.fields.birthday'),
                'value' => $user->birthday ?? ($data['birthday'] ?? ''),
            ])

            @include('components.select', [
                'name' => 'role',
                'label' => __('admin/user.fields.role'),
                'icon' => 'user-shield',
                'required' => true,
                'value' => $user->role ?? ($data['role'] ?? \App\Const\UserConst::ROLE_USER),
                'options' => \App\Const\UserConst::roles(),
            ])

            @include('components.select', [
                'name' => 'status',
                'label' => __('admin/user.fields.status'),
                'icon' => 'toggle-on',
                'required' => true,
                'value' => $user->status ?? ($data['status'] ?? \App\Const\UserConst::STATUS_ACTIVE),
                'options' => \App\Const\UserConst::statuses(),
            ])
        </div>
    </section>

    <section
        class="bg-white rounded-xl shadow-sm border overflow-hidden {{ $errors->has('user_addresses') ? 'border-red-300' : 'border-gray-100' }}">
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100 bg-gray-50/60">
            <span class="flex items-center gap-3">
                <span class="w-8 h-8 shrink-0 flex items-center justify-center rounded-full bg-blue-500 text-white text-sm font-semibold">3</span>
                <span>
                    <span class="block font-semibold text-gray-900">{{ __('admin/user.sections.address') }}</span>
                    <span id="address-hint" class="block text-sm text-gray-500">
                        {{ __('admin/user.sections.address_hint', ['max' => $maxAddresses, 'count' => count($currentAddresses)]) }}
                    </span>
                </span>
            </span>

            <button type="button" id="add-address-btn"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                <i class="fa-solid fa-plus"></i>
                {{ __('admin/user.address.add') }}
            </button>
        </header>

        <div class="p-5 space-y-4">
            <div id="address-container" class="space-y-4">
                @foreach ($currentAddresses as $index => $address)
                    @include('components.address-form', ['index' => $index, 'provinces' => $provinces])
                @endforeach
            </div>

            <template id="address-template">
                @include('components.address-form', [
                    'index' => 'INDEX',
                    'provinces' => $provinces,
                    'address' => [],
                ])
            </template>

            <p class="flex items-start gap-2 text-sm text-gray-600 bg-amber-50 border border-amber-100 rounded-lg p-3">
                <i class="fa-solid fa-circle-info text-amber-500 mt-0.5"></i>
                {{ __('admin/user.hints.default_address') }}
            </p>

            @error('user_addresses')
                <p class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg p-3">{{ $message }}</p>
            @enderror
        </div>
    </section>

    <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <header class="flex items-center gap-3 px-5 py-4 border-b border-gray-100 bg-gray-50/60">
            <span class="w-8 h-8 shrink-0 flex items-center justify-center rounded-full bg-blue-500 text-white text-sm font-semibold">4</span>
            <span>
                <span class="block font-semibold text-gray-900">{{ __('admin/user.sections.bank') }}</span>
                <span class="block text-sm text-gray-500">{{ __('admin/user.sections.bank_hint') }}</span>
            </span>
        </header>

        <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-5">
            @include('components.select', [
                'name' => 'bank_name',
                'label' => __('admin/user.fields.bank_name'),
                'icon' => 'building-columns',
                'options' => \App\Const\BankConst::getOptions(),
                'value' => $user->bank_name ?? ($data['bank_name'] ?? ''),
                'placeholder' => __('common.labels.none'),
            ])

            @include('components.input', [
                'name' => 'user_bank_name',
                'label' => __('admin/user.fields.user_bank_name'),
                'icon' => 'user',
                'value' => $user->user_bank_name ?? ($data['user_bank_name'] ?? ''),
            ])

            @include('components.input', [
                'name' => 'bank_account',
                'label' => __('admin/user.fields.bank_account'),
                'icon' => 'credit-card',
                'value' => $user->bank_account ?? ($data['bank_account'] ?? ''),
            ])
        </div>
    </section>

    <div class="flex flex-col sm:flex-row justify-end gap-3">
        <a href="{{ route('admin.users.index') }}"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            {{ __('admin/user.buttons.back_to_list') }}
        </a>

        <button type="submit"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
            <i class="fa-solid fa-arrow-right"></i>
            {{ __('common.actions.confirm') }}
        </button>
    </div>
</form>

@once
    @push('scripts')
        <script>
            $(function() {
                const MAX = {{ $maxAddresses }};
                const HINT = @json(__('admin/user.sections.address_hint', ['max' => $maxAddresses, 'count' => ':n']));
                const LOADING = @json(__('admin/user.hints.loading'));
                const LOAD_FAILED = @json(__('admin/user.hints.load_failed'));
                const SELECT_WARD = @json(__('admin/user.address.select_ward'));

                const $container = $('#address-container');
                const template = $('#address-template').html();

                function refresh() {
                    const count = $container.find('.address-item').length;
                    $('#address-hint').text(HINT.replace(':n', count));
                    $('#add-address-btn').toggle(count < MAX);

                    $container.find('.address-item').each(function(position) {
                        $(this).find('.remove-address-btn').toggle(count > 1);
                        $(this).find('.address-title').text(position + 1);
                    });
                }

                $('#add-address-btn').on('click', function() {
                    if ($container.find('.address-item').length >= MAX) {
                        return;
                    }

                    $container.append(template.replace(/INDEX/g, Date.now()));
                    refresh();
                });

                $container.on('click', '.remove-address-btn', function() {
                    $(this).closest('.address-item').remove();
                    refresh();
                });

                $container.on('change', '.province-select', function() {
                    const provinceId = $(this).val();
                    const $ward = $(this).closest('.address-item').find('.ward-select');

                    $ward.prop('disabled', true).html(`<option value="">${LOADING}</option>`);

                    if (!provinceId) {
                        $ward.html(`<option value="">${SELECT_WARD}</option>`);
                        return;
                    }

                    $.getJSON(`/api/get-wards/${provinceId}`)
                        .done(function(wards) {
                            let options = `<option value="">${SELECT_WARD}</option>`;
                            wards.forEach(w => options += `<option value="${w.id}">${w.name}</option>`);
                            $ward.html(options).prop('disabled', false);
                        })
                        .fail(function() {
                            $ward.html(`<option value="">${LOAD_FAILED}</option>`);
                        });
                });

                $container.on('change', '.address-default-checkbox', function() {
                    if ($(this).is(':checked')) {
                        $container.find('.address-default-checkbox').not(this).prop('checked', false);
                    }
                });

                refresh();
            });
        </script>
    @endpush
@endonce
