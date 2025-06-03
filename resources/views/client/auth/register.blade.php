@extends('client.layouts.master-basic')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="d-flex justify-content-center mb-4">
                    <a href="{{ route('index') }}" class="logo d-flex align-items-center text-decoration-none">
                        <img src="views/logo.png" alt="Alibubu Logo" class="img-fluid rounded me-2" style="height: 50px;">
                    </a>
                </div>

                <div class="p-4 shadow rounded bg-white">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold">Create an Account</h4>
                        <p class="text-muted small">Enter your personal details to create an account</p>
                    </div>

                    <form class="row g-3" method="POST">
                        @csrf

                        <div class="col-12">
                            <label for="yourName" class="form-label">Full Name</label>
                            <input type="text" name="fullname"
                                class="form-control @error('fullname') is-invalid @enderror" id="yourName"
                                value="{{ old('fullname') }}" placeholder="John Doe">
                            @error('fullname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="yourEmail" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
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
                            <label for="yourPasswordConfirm" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation"
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                id="yourPasswordConfirm" placeholder="******">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mt-3">
                            <button class="btn btn-primary w-100 py-2" type="submit">Create Account</button>
                        </div>

                        <div class="col-12 mt-2 mb-2">
                            <a href="{{ route('auth.client.redirectToGoogle') }}"
                                class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="fa-brands fa-google mr-2"></i>
                                Register with Google
                            </a>
                        </div>

                        <div class="col-12 text-center">
                            <p class="small mb-0 mt-2">Already have an account? <a
                                    href="{{ route('auth.client.showFormLogin') }}">Log in</a></p>
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
