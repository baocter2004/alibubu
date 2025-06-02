@extends('admin.layouts.master-basic')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10">

                <div class="d-flex justify-content-center py-4">
                    <a href="{{ route('admin.dashboard') }}" class="logo d-flex align-items-center w-auto">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('views/logo.png') }}" alt="Admin Logo" class="img-fluid rounded me-2"
                                style="height: 40px;">
                            <span class="d-none d-lg-block ml-2 fw-bold text-primary fs-4">Admin Panel</span>
                        </div>
                    </a>
                </div>

                <div class="p-4 shadow rounded bg-white">
                    <div class="text-center mb-4">
                        <h5 class="fw-bold">Admin / Employee Login</h5>
                        <p class="text-muted small">Enter your Email & password to access the admin panel</p>
                    </div>

                    <form class="row g-3 needs-validation" method="POST" action="">
                        @csrf
                        <div class="col-12">
                            <label for="yourEmail" class="form-label">Email</label>
                            <input type="text" name="email" class="form-control @error('email') is-invalid @enderror"
                                id="yourEmail" value="{{ old('email') }}" placeholder="name@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="yourPassword" class="form-label">Password</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" id="yourPassword"
                                placeholder="******">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" value="true"
                                    id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Remember me</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <button class="btn btn-primary w-100" type="submit">Login</button>
                        </div>

                        <div class="col-12">
                            <p class="small mb-0">Bạn không có tài khoản? Liên hệ quản trị viên để được cấp quyền.</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: "{{ session('success') }}",
                    timer: 2500,
                    showConfirmButton: false
                });
            @elseif (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: "{{ session('error') }}",
                    timer: 2500,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@endpush
