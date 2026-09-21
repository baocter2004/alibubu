@include('errors.partials.card', [
    'code' => 404,
    'variant' => 'admin',
    'homeUrl' => \Illuminate\Support\Facades\Auth::guard('admin')->check() ? route('admin.dashboard') : route('auth.admin.showFormLogin'),
])
