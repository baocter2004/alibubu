<div class="site-navbar-top">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-6 col-md-4 order-2 order-md-1 site-search-icon text-left">
                <form action="" class="site-block-top-search">
                    <span class="icon icon-search2"></span>
                    <input type="text" class="form-control border-0" placeholder="Tìm Kiếm ...">
                </form>
            </div>

            <div class="col-12 mb-3 mb-md-0 col-md-4 order-1 order-md-2 text-center">
                <div class="site-logo">
                    <a href="{{ route('index') }}" class="js-logo-clone">Shoppers</a>
                </div>
            </div>

            <div class="col-6 col-md-4 order-3 order-md-3 text-right">
                <div class="site-top-icons">
                    <ul>
                        @if (Auth::user())
                            <li class="nav-item">
                                <p>{{ Auth::user()->fullname }}</p>
                            </li>
                        @else
                            <li class="nav-item">
                                <p>Login / Register</p>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown"
                                aria-expanded="false">
                                <span class="icon icon-person"></span>
                            </a>
                            @if (Auth::user())
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item w-100" href="#">Thông Tin Cá Nhân</a></li>
                                    <li><a class="dropdown-item w-100" href="{{ route('auth.client.logout') }}">Đăng
                                            Xuất</a></li>
                                    @if (!Auth::user()->hasVerifiedEmail())
                                        <li><a class="dropdown-item w-100 verify_button" href="">Xác Minh
                                                Email</a></li>
                                    @endif
                                </ul>
                            @else
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('auth.client.showFormLogin') }}"
                                            class="dropdown-item">Login</a>
                                        <a href="{{ route('auth.client.showFormRegister') }}"
                                            class="dropdown-item">Register</a>
                                    </li>
                                </ul>
                            @endif
                        </li>
                        <li><a href="#"><span class="icon icon-heart-o"></span></a></li>
                        <li>
                            <a href="cart.html" class="site-cart">
                                <span class="icon icon-shopping_cart"></span>
                                <span class="count">2</span>
                            </a>
                        </li>
                        <li class="d-inline-block d-md-none ml-md-0"><a href="#"
                                class="site-menu-toggle js-menu-toggle"><span class="icon-menu"></span></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.verify_button').on('click', function(e) {
            e.preventDefault();
            $.ajax({
                url: "/api/email/resend",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Authorization': 'Bearer ' + window.userToken,
                    'Accept': 'application/json'
                },
                data: {
                    user_id: {{ Auth::id() }}
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: response.message,
                        timer: 2500,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    let message = xhr.responseJSON?.message || 'Đã có lỗi xảy ra.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: message,
                        timer: 2500,
                        showConfirmButton: false
                    });
                }
            });
        });
    });
</script>
