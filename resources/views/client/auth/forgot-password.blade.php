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
                        <h5 class="fw-bold">Forgot Password?</h5>
                        <p class="text-muted small">Enter your email to reset your password</p>
                    </div>

                    <form method="POST" action="" class="row g-3 needs-validation">
                        @csrf
                        <div class="col-12">
                            <label for="yourEmail" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                id="yourEmail" value="{{ old('email') }}" placeholder="name@example.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mt-3 mb-2">
                            <button class="btn btn-primary w-100" type="submit">Send Password Reset Link</button>
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
