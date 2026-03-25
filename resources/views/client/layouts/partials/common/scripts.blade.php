<script src="{{ asset('assets/js/sweetalert2@11.js') }}"></script>
<script src="{{ asset('assets/js/flatpickr.all.min.js') }}"></script>
<script src="{{ asset('assets/js/html2pdf.bundle.min.js') }}"></script>

<script>
    $(document).ready(function() {
    @if (session('success'))
        Swal.fire({
        icon: 'success',
        title: 'Thành công!',
        text: "{{ session('success') }}",
        confirmButtonText: 'OK'
        });
    @endif
    @if (session('error'))
        Swal.fire({
        icon: 'error',
        title: 'Thất Bại!',
        text: "{{ session('error') }}",
        confirmButtonText: 'OK'
        });
    @endif
    });
</script>
