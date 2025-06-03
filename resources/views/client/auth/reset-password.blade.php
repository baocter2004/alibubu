@extends('client.layouts.master-basic')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="d-flex justify-content-center py-4">
                    <a href="{{ route('index') }}" class="logo d-flex align-items-center w-auto">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('views/logo.png') }}" alt="Alibubu Logo" class="img-fluid rounded me-2"
                                style="height: 40px;">
                        </div>
                    </a>
                </div>

                <div class="p-4 shadow rounded bg-white">
                    <div class="text-center mb-4">
                        <h5 class="fw-bold">Reset Password</h5>
                        <p class="text-muted small">Enter your new password below</p>
                    </div>

                    <form method="POST" action="{{ route('auth.client.reset') }}" class="row g-3 needs-validation">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="col-12">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" name="password"
                                class="form-control mt-2 @error('password') is-invalid @enderror" id="password"
                                placeholder="Enter new password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control mt-2"
                                id="password_confirmation" placeholder="Confirm new password">
                        </div>

                        <div class="col-12 mt-3 mb-2">
                            <button class="btn btn-primary w-100" type="submit">Reset Password</button>
                        </div>

                        <div class="col-12 text-center">
                            <p class="small mb-0">
                                Remember your password? <a href="{{ route('auth.client.showFormLogin') }}">Login here</a>
                            </p>
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
