<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
            1
        </div>
        <div>
            <h2 class="text-lg font-semibold text-gray-800">
                Thông Tin Cơ Bản
            </h2>
            <p class="text-sm text-gray-500">
                Nhập thông tin chi tiết của chi nhánh
            </p>
        </div>
    </div>

    <div class="-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
        @include('components.input', [
            'name' => 'logo',
            'label' => 'Logo Chi Nhánh',
            'icon' => 'image',
            'placeholder' => 'Chọn logo cho chi nhánh',
            'value' => $branch->logo ?? ($data['logo'] ?? ''),
            'type' => 'file',
            'required' => true,
        ])
    </div>

    <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @include('components.input', [
                'name' => 'name',
                'required' => true,
                'label' => 'Tên Chi Nhánh',
                'placeholder' => 'Mời nhập tên chi nhánh',
                'value' => $branch->name ?? ($data['name'] ?? ''),
                'class' => 'name-input',
                'icon' => 'building',
            ])
            @include('components.input', [
                'name' => 'slug',
                'required' => true,
                'label' => 'Mã Chi Nhánh',
                'placeholder' => 'Mời nhập mã chi nhánh (Tự động chuyển về chữ thường và dấu gạch ngang)',
                'value' => $branch->slug ?? ($data['slug'] ?? ''),
                'class' => 'slug-input',
                'icon' => 'code',
                'readonly' => true,
            ])
        </div>


        <div class="text-sm font-medium text-gray-700">
            <i class="fa-solid fa-triangle-exclamation text-yellow-500 mr-1"></i>
            Lưu ý: Mã chi nhánh sẽ được tự động tạo dựa trên tên chi nhánh và có thể chỉnh sửa sau khi đã tạo.
            Vui lòng đảm bảo tên chi nhánh là duy nhất và phù hợp với quy định đặt tên của hệ thống.
        </div>
    </div>

    <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
            2
        </div>
        <div>
            <h2 class="text-lg font-semibold text-gray-800">
                Thông Tin Chi Tiết
            </h2>
            <p class="text-sm text-gray-500">
                Thông tin bổ sung
            </p>
        </div>
    </div>
    <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
        <div class="grid grid-cols-1 gap-4">
            @include('components.select', [
                'name' => 'is_active',
                'label' => 'Trạng thái',
                'icon' => 'toggle-on',
                'placeholder' => 'Chọn trạng thái',
                'value' => $branch->is_active ?? ($data['is_active'] ?? ''),
                'options' => \App\Const\GlobalConst::STATUS,
                'required' => true,
            ])
        </div>
    </div>

    <div class="w-full p-4 mt-4 bg-white rounded-lg shadow-lg grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-4">

        <!-- Back -->
        <a href="{{ route('admin.branches.index') }}"
            class="w-full flex justify-center items-center gap-2 p-2 md:p-4 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 hover:text-gray-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300">

            <i class="fa-solid fa-arrow-left"></i>
            Quay Lại Danh Sách
        </a>

        <!-- Submit -->
        <button type="submit"
            class="w-full flex justify-center items-center gap-2 p-2 md:p-4 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">

            <i class="fa-solid fa-plus"></i>
            {{ empty($branch->id) ? 'Tạo chi nhánh' : 'Cập Nhật Thông Tin' }}
        </button>
    </div>
</form>

@once
    @push('scripts')
        <script>
            function generateSlug(text) {
                return text
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/đ/g, 'd')
                    .replace(/[^a-z0-9]+/g, '-')
                    .trim()
                    .replace(/^-+|-+$/g, '')
                    .replace(/-+/g, '-');
            }
            document.addEventListener('DOMContentLoaded', function() {
                const nameInput = document.querySelector('.name-input');
                const slugInput = document.querySelector('.slug-input');

                if (nameInput && slugInput) {
                    nameInput.addEventListener('input', function() {
                        const slug = generateSlug(nameInput.value);
                        console.log('Generated slug:', slug);
                        slugInput.value = slug;
                    });
                }
            });
        </script>
    @endpush
@endonce
