@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client.account.nav.addresses'))

@section('content')
    <nav class="flex items-center gap-2 text-sm text-muted-foreground mb-6">
        <a href="{{ route('index') }}" class="hover:text-primary transition-colors">{{ __('client.nav.home') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-foreground font-medium">{{ __('client.account.nav.addresses') }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6 items-start">
        @include('client.pages.account.nav')

        <div class="flex-1 min-w-0 space-y-6">
            <section class="bg-card border border-border rounded-2xl p-5 md:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div>
                        <h1 class="text-lg font-bold text-foreground">{{ __('client.account.addresses.title') }}</h1>
                        <p class="text-sm text-muted-foreground mt-0.5">{{ __('client.account.addresses.subtitle') }}</p>
                    </div>

                    <button type="button" id="toggle-address-form"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold btn-primary rounded-lg">
                        <i class="fa-solid fa-plus"></i>
                        {{ __('client.account.addresses.add') }}
                    </button>
                </div>

                <form action="{{ route('account.addresses.store') }}" method="POST" id="address-form"
                    data-store-url="{{ route('account.addresses.store') }}"
                    class="{{ $errors->any() ? '' : 'hidden' }} bg-muted/40 border border-border rounded-xl p-5 mb-6 space-y-4">
                    @csrf
                    <input type="hidden" name="_method" id="address-form-method" value="POST">

                    <p class="text-sm font-bold text-foreground" id="address-form-title">
                        {{ __('client.account.addresses.add') }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="fullname" class="block text-sm font-medium text-foreground mb-1.5">
                                {{ __('client.account.fields.recipient') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="fullname" name="fullname" value="{{ old('fullname') }}"
                                class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('fullname') ? 'is-invalid' : 'border-border' }}">
                            @error('fullname')
                                <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-foreground mb-1.5">
                                {{ __('client.account.fields.phone_number') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="phone_number" name="phone_number"
                                value="{{ old('phone_number') }}"
                                class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('phone_number') ? 'is-invalid' : 'border-border' }}">
                            @error('phone_number')
                                <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="province_id" class="block text-sm font-medium text-foreground mb-1.5">
                                {{ __('client.account.fields.province') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="province_id" name="province_id"
                                class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('province_id') ? 'is-invalid' : 'border-border' }}">
                                <option value="">{{ __('client.account.fields.select_province') }}</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->id }}" @selected((string) old('province_id') === (string) $province->id)>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('province_id')
                                <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="ward_id" class="block text-sm font-medium text-foreground mb-1.5">
                                {{ __('client.account.fields.ward') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="ward_id" name="ward_id" @disabled(! old('province_id'))
                                class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('ward_id') ? 'is-invalid' : 'border-border' }}">
                                <option value="">{{ __('client.account.fields.select_ward') }}</option>
                            </select>
                            @error('ward_id')
                                <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-foreground mb-1.5">
                                {{ __('client.account.fields.address') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="address" name="address" value="{{ old('address') }}"
                                class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('address') ? 'is-invalid' : 'border-border' }}">
                            @error('address')
                                <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_default" value="1" @checked(old('is_default'))
                            class="h-4 w-4 rounded accent-primary">
                        <span class="text-sm text-muted-foreground">{{ __('client.account.addresses.set_default') }}</span>
                    </label>

                    <div class="flex justify-end gap-3">
                        <button type="button" id="cancel-address-form"
                            class="px-5 py-2.5 text-sm font-medium text-muted-foreground border border-border rounded-lg hover:bg-muted transition-colors">
                            {{ __('common.actions.cancel') }}
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold btn-primary rounded-lg">
                            <i class="fa-solid fa-floppy-disk"></i>
                            {{ __('common.actions.save') }}
                        </button>
                    </div>
                </form>

                @if ($addresses->isEmpty())
                    <div class="py-14 text-center">
                        <i class="fa-solid fa-location-dot text-5xl text-muted-foreground/25 mb-4"></i>
                        <p class="text-foreground font-medium">{{ __('client.account.addresses.empty') }}</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($addresses as $address)
                            <div
                                class="border rounded-xl p-4 {{ $address->is_default ? 'border-primary/40 bg-primary/5' : 'border-border' }}">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <p class="font-semibold text-foreground">{{ $address->fullname }}</p>
                                    @if ($address->is_default)
                                        <span
                                            class="px-2 py-0.5 text-[11px] font-semibold rounded-full bg-primary text-white shrink-0">
                                            {{ __('client.account.addresses.default_badge') }}
                                        </span>
                                    @endif
                                </div>

                                <p class="text-sm text-muted-foreground mb-1">
                                    <i class="fa-solid fa-phone text-muted-foreground/60 mr-1.5"></i>{{ $address->phone_number }}
                                </p>
                                <p class="text-sm text-muted-foreground mb-3">
                                    <i class="fa-solid fa-location-dot text-muted-foreground/60 mr-1.5"></i>{{ $address->full_address }}
                                </p>

                                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 pt-3 border-t border-border/70">
                                    <button type="button" class="edit-address text-xs font-medium text-primary hover:underline"
                                        data-id="{{ $address->id }}"
                                        data-url="{{ route('account.addresses.update', $address->id) }}"
                                        data-fullname="{{ $address->fullname }}"
                                        data-phone="{{ $address->phone_number }}"
                                        data-province="{{ $address->province_id }}"
                                        data-ward="{{ $address->ward_id }}"
                                        data-address="{{ $address->address }}"
                                        data-default="{{ $address->is_default ? 1 : 0 }}">
                                        <i class="fa-solid fa-pen mr-1"></i>{{ __('common.actions.edit') }}
                                    </button>

                                    @unless ($address->is_default)
                                        <form action="{{ route('account.addresses.update', $address->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="fullname" value="{{ $address->fullname }}">
                                            <input type="hidden" name="phone_number" value="{{ $address->phone_number }}">
                                            <input type="hidden" name="province_id" value="{{ $address->province_id }}">
                                            <input type="hidden" name="ward_id" value="{{ $address->ward_id }}">
                                            <input type="hidden" name="address" value="{{ $address->address }}">
                                            <input type="hidden" name="is_default" value="1">
                                            <button type="submit"
                                                class="text-xs font-medium text-muted-foreground hover:text-primary transition-colors">
                                                <i class="fa-regular fa-star mr-1"></i>{{ __('client.account.addresses.set_default') }}
                                            </button>
                                        </form>
                                    @endunless

                                    <form action="{{ route('account.addresses.destroy', $address->id) }}" method="POST"
                                        class="ml-auto"
                                        onsubmit="return confirm('{{ __('client.account.messages.delete_address_confirm') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-xs font-medium text-muted-foreground hover:text-red-500 transition-colors">
                                            <i class="fa-regular fa-trash-can mr-1"></i>{{ __('common.actions.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            const SELECT_WARD = @json(__('client.account.fields.select_ward'));
            const oldWard = @json(old('ward_id'));

            const $form = $('#address-form');

            function resetForm() {
                $form[0].reset();
                $form.attr('action', $form.data('store-url'));
                $('#address-form-method').val('POST');
                $('#address-form-title').text(@json(__('client.account.addresses.add')));
                $('#ward_id').prop('disabled', true).html(`<option value="">${SELECT_WARD}</option>`);
            }

            $('#toggle-address-form').on('click', function() {
                resetForm();
                $form.removeClass('hidden');
                $('#fullname').trigger('focus');
            });

            $('#cancel-address-form').on('click', function() {
                $form.addClass('hidden');
                resetForm();
            });

            $('.edit-address').on('click', function() {
                const data = $(this).data();

                $form.attr('action', data.url).removeClass('hidden');
                $('#address-form-method').val('PATCH');
                $('#address-form-title').text(@json(__('client.account.addresses.edit')));
                $('#fullname').val(data.fullname);
                $('#phone_number').val(data.phone);
                $('#address').val(data.address);
                $('#province_id').val(data.province);
                $('input[name=is_default]').prop('checked', Number(data.default) === 1);

                loadWards(data.province, data.ward);

                $('html, body').animate({ scrollTop: $form.offset().top - 120 }, 300);
            });

            function loadWards(provinceId, selected) {
                const $ward = $('#ward_id');

                if (!provinceId) {
                    $ward.prop('disabled', true).html(`<option value="">${SELECT_WARD}</option>`);
                    return;
                }

                $.getJSON(`/api/get-wards/${provinceId}`).done(function(wards) {
                    let options = `<option value="">${SELECT_WARD}</option>`;
                    wards.forEach(w => options +=
                        `<option value="${w.id}" ${String(w.id) === String(selected) ? 'selected' : ''}>${w.name}</option>`);
                    $ward.html(options).prop('disabled', false);
                });
            }

            $('#province_id').on('change', function() {
                loadWards($(this).val(), null);
            });

            if ($('#province_id').val()) {
                loadWards($('#province_id').val(), oldWard);
            }
        });
    </script>
@endpush
