@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client.account.nav.profile'))

@section('content')
    <nav class="flex items-center gap-2 text-sm text-muted-foreground mb-6">
        <a href="{{ route('index') }}" class="hover:text-primary transition-colors">{{ __('client.nav.home') }}</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-foreground font-medium">{{ __('client.account.nav.profile') }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6 items-start">
        @include('client.pages.account.nav')

        <div class="flex-1 min-w-0 space-y-6">
            <section class="bg-card border border-border rounded-2xl p-5 md:p-6">
                <h1 class="text-lg font-bold text-foreground mb-1">{{ __('client.account.profile.title') }}</h1>
                <p class="text-sm text-muted-foreground mb-6">{{ __('client.account.profile.subtitle') }}</p>

                <form action="{{ route('account.profile.update') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="fullname" class="block text-sm font-medium text-foreground mb-1.5">
                                {{ __('client.account.fields.fullname') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="fullname" name="fullname"
                                value="{{ old('fullname', $user->fullname) }}"
                                class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('fullname') ? 'is-invalid' : 'border-border' }}">
                            @error('fullname')
                                <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1.5">
                                {{ __('client.account.fields.email') }}
                            </label>
                            <input type="email" value="{{ $user->email }}" disabled
                                class="w-full px-4 py-2.5 text-sm border border-border rounded-lg bg-muted text-muted-foreground">
                        </div>

                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-foreground mb-1.5">
                                {{ __('client.account.fields.phone_number') }}
                            </label>
                            <input type="tel" id="phone_number" name="phone_number"
                                value="{{ old('phone_number', $user->phone_number) }}"
                                class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('phone_number') ? 'is-invalid' : 'border-border' }}">
                            @error('phone_number')
                                <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-foreground mb-1.5">
                                {{ __('client.account.fields.gender') }}
                            </label>
                            <select id="gender" name="gender"
                                class="w-full px-4 py-2.5 text-sm border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                                <option value="">{{ __('common.labels.none') }}</option>
                                @foreach (\App\Const\UserConst::genders() as $key => $label)
                                    <option value="{{ $key }}" @selected((string) old('gender', $user->gender) === (string) $key)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="birthday" class="block text-sm font-medium text-foreground mb-1.5">
                                {{ __('client.account.fields.birthday') }}
                            </label>
                            <input type="date" id="birthday" name="birthday"
                                value="{{ old('birthday', $user->birthday?->format('Y-m-d')) }}"
                                class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('birthday') ? 'is-invalid' : 'border-border' }}">
                            @error('birthday')
                                <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold btn-primary rounded-xl">
                            <i class="fa-solid fa-floppy-disk"></i>
                            {{ __('common.actions.save') }}
                        </button>
                    </div>
                </form>
            </section>

            <section class="bg-card border border-border rounded-2xl p-5 md:p-6">
                <h2 class="text-lg font-bold text-foreground mb-1">{{ __('client.account.profile.password_title') }}</h2>
                <p class="text-sm text-muted-foreground mb-6">{{ __('client.account.profile.password_subtitle') }}</p>

                <form action="{{ route('account.password.update') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        @foreach ([['current_password', 'current_password'], ['password', 'new_password'], ['password_confirmation', 'confirm_password']] as [$field, $labelKey])
                            <div>
                                <label for="{{ $field }}" class="block text-sm font-medium text-foreground mb-1.5">
                                    {{ __('client.account.fields.' . $labelKey) }} <span class="text-red-500">*</span>
                                </label>
                                <input type="password" id="{{ $field }}" name="{{ $field }}"
                                    autocomplete="{{ $field === 'current_password' ? 'current-password' : 'new-password' }}"
                                    class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has($field) ? 'is-invalid' : 'border-border' }}">
                                @error($field)
                                    <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold btn-primary rounded-xl">
                            <i class="fa-solid fa-key"></i>
                            {{ __('client.account.profile.password_title') }}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endsection
