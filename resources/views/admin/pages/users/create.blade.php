@extends('admin.layouts.app')

@section('title')
    Trang thêm mới người dùng
@endsection

@section('content')
    <div class="max-w-5xl m-auto">
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Thêm mới người dùng</h1>
            <p class="text-gray-600 mt-1">Điền đầy đủ thông tin để tạo tài khoản mới.</p>
        </div>
        @include('admin.pages.users.form', ['formAction' => route('admin.users.confirm')])
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('address-container');
            const template = document.getElementById('address-template').innerHTML;
            const addBtn = document.getElementById('add-address-btn');
            const countSpan = document.getElementById('address-count');

            function updateUI() {
                const items = container.querySelectorAll('.address-item');
                countSpan.innerText = items.length;
                addBtn.style.display = items.length >= 5 ? 'none' : 'inline-flex';
            }

            // 1. Thêm địa chỉ
            addBtn.addEventListener('click', function() {
                if (container.querySelectorAll('.address-item').length < 5) {
                    const index = Date.now(); // Tạo index duy nhất
                    const html = template.replace(/INDEX/g, index);
                    container.insertAdjacentHTML('beforeend', html);
                    updateUI();
                }
            });

            // 2. Xóa địa chỉ
            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-address-btn')) {
                    e.target.closest('.address-item').remove();
                    updateUI();
                }
            });

            // 3. Load Phường/Xã khi chọn Tỉnh
            container.addEventListener('change', async function(e) {
                if (e.target.classList.contains('province-select')) {
                    const provinceId = e.target.value;
                    const wardSelect = e.target.closest('.address-item').querySelector('.ward-select');

                    console.log(provinceId)

                    wardSelect.innerHTML = '<option value="">Đang tải...</option>';
                    wardSelect.disabled = true;

                    if (provinceId) {
                        try {
                            const res = await fetch(`/api/get-wards/${provinceId}`);
                            const wards = await res.json();
                            console.log(wards)

                            let options = '<option value="">-- Chọn xã --</option>';
                            wards.forEach(w => options += `<option value="${w.id}">${w.name}</option>`);
                            wardSelect.innerHTML = options;
                            wardSelect.disabled = false;
                        } catch (err) {
                            wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                        }
                    }
                }
            });

            // 4. Chỉ cho phép chọn 1 địa chỉ mặc định
            container.addEventListener('change', function(e) {
                if (e.target.classList.contains('address-default-checkbox') && e.target.checked) {
                    container.querySelectorAll('.address-default-checkbox').forEach(cb => {
                        if (cb !== e.target) cb.checked = false;
                    });
                }
            });

            updateUI(); // Khởi tạo số lượng lúc đầu
        });
    </script>
@endpush
