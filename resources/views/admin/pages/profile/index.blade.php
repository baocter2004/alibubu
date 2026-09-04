@extends('admin.layouts.app')

@section('title', __('admin/profile.title'))

@section('content')
    @include('admin.partials.page-header', [
        'title' => __('admin/profile.title'),
        'subtitle' => __('admin/profile.subtitle'),
        'crumbs' => [['label' => __('admin/profile.title')]],
    ])

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <section class="bg-white border border-gray-200 rounded-2xl p-5 md:p-6">
            <div class="flex items-center gap-3 mb-5">
                <span class="w-11 h-11 shrink-0 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fa-solid fa-user-pen"></i>
                </span>
                <div class="min-w-0">
                    <h2 class="font-semibold text-gray-900">{{ __('admin/profile.sections.information') }}</h2>
                    <p class="text-sm text-gray-500">{{ __('admin/profile.sections.information_hint') }}</p>
                </div>
            </div>

            <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-5">
                @csrf
                @method('PATCH')

                @include('components.input', [
                    'label' => __('admin/profile.fields.name'),
                    'name' => 'name',
                    'value' => $admin->name,
                    'required' => true,
                    'icon' => 'user',
                ])

                @include('components.input', [
                    'label' => __('admin/profile.fields.email'),
                    'name' => 'email',
                    'type' => 'email',
                    'value' => $admin->email,
                    'required' => true,
                    'icon' => 'envelope',
                ])

                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-blue-500 rounded-xl hover:bg-blue-600 transition-colors">
                        <i class="fa-solid fa-floppy-disk"></i>
                        {{ __('common.actions.save') }}
                    </button>
                </div>
            </form>
        </section>

        <section id="password" class="bg-white border border-gray-200 rounded-2xl p-5 md:p-6 scroll-mt-24">
            <div class="flex items-center gap-3 mb-5">
                <span class="w-11 h-11 shrink-0 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-key"></i>
                </span>
                <div class="min-w-0">
                    <h2 class="font-semibold text-gray-900">{{ __('admin/profile.sections.password') }}</h2>
                    <p class="text-sm text-gray-500">{{ __('admin/profile.sections.password_hint') }}</p>
                </div>
            </div>

            <form action="{{ route('admin.profile.password.update') }}" method="POST" class="space-y-5">
                @csrf
                @method('PATCH')

                @include('components.input', [
                    'label' => __('admin/profile.fields.current_password'),
                    'name' => 'current_password',
                    'type' => 'password',
                    'required' => true,
                    'icon' => 'lock',
                ])

                @include('components.input', [
                    'label' => __('admin/profile.fields.new_password'),
                    'name' => 'password',
                    'type' => 'password',
                    'required' => true,
                    'icon' => 'lock',
                ])

                @include('components.input', [
                    'label' => __('admin/profile.fields.confirm_password'),
                    'name' => 'password_confirmation',
                    'type' => 'password',
                    'required' => true,
                    'icon' => 'lock',
                ])

                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-blue-500 rounded-xl hover:bg-blue-600 transition-colors">
                        <i class="fa-solid fa-key"></i>
                        {{ __('admin/nav.change_password') }}
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
