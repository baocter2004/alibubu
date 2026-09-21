@include('errors.partials.card', [
    'code' => 500,
    'variant' => 'admin',
    'homeUrl' => \Illuminate\Support\Facades\Auth::guard('admin')->check() ? route('admin.dashboard') : route('auth.admin.showFormLogin'),
])
