@extends('client.layouts.master-basic')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10">

                <div class="d-flex justify-content-center py-4">
                    <a href="{{ route('index') }}" class="logo d-flex align-items-center w-auto">
                        <div class="d-flex align-items-center">
                            <img src="views/logo.png" alt="Alibubu Logo" class="img-fluid rounded me-2" style="height: 40px;">
                        </div>
                    </a>
                </div>

                <div class="p-4 shadow rounded bg-white">
                    <div class="text-center mb-4">
                        <h5 class="fw-bold">Login to Your Account</h5>
                        <p class="text-muted small">Enter your Email & password to login</p>
                    </div>

                    <form class="row g-3 needs-validation" method="POST">
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
                                <input class="form-check-input cursor-pointer @error('remember') is-invalid @enderror"
                                    type="checkbox" name="remember" value="1" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Remember me</label>
                                @error('remember')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <button class="btn btn-primary w-100" type="submit">Login</button>
                        </div>

                        <div class="col-12 mt-2 mb-2">
                            <a href="{{ route('auth.client.redirectToGoogle') }}"
                                class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="fa-brands fa-google mr-2"></i>
                                Login with Google
                            </a>
                        </div>

                        <div class="row w-100 d-flex justify-center items-center">
                            <div class="col-sm-6">
                                <p class="small mb-0">
                                    Don't have an account?
                                    <a href="{{ route('auth.client.showFormRegister') }}">Create an account</a>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <p class="small mb-0">
                                    Forgot your password?
                                    <a href="{{ route('auth.client.showFormForgotPassword') }}">Click here to reset</a>
                                </p>
                            </div>
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
