# Alibubu

Ứng dụng thương mại điện tử xây dựng trên Laravel 12, gồm hai khu vực: storefront (khách hàng) và trang quản trị (admin), dùng chung một cơ sở dữ liệu.

## Công nghệ sử dụng

- PHP 8.2+, Laravel 12
- MySQL (production), SQLite (test, chạy trong bộ nhớ)
- Laravel Sanctum (API), Laravel Socialite (đăng nhập Google)
- Vite 6 + Tailwind CSS 4
- Hàng đợi (queue) chạy trên driver `database`, session lưu trong bảng `sessions`

## Kiến trúc

- `Controller -> Service (app/Services) -> Repository (app/Repositories)`
- Hằng số nghiệp vụ đặt tại `app/Const/<Domain>Const.php`, không khai báo hằng số rải rác trong service/controller.
- Request validation nằm trong `app/Http/Requests`.
- Chuỗi hiển thị cho người dùng được dịch song song ở `lang/vi` và `lang/en`.
- Phân quyền admin dùng Gate/Policy theo từng "ability" (`can:<ability>` trên route, `@can('<ability>')` trong Blade); super admin luôn được phép qua `Gate::before`.

## Cài đặt

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate
```

Cập nhật `.env` theo môi trường (xem chi tiết ở mục **Biến môi trường** bên dưới), sau đó:

```bash
php artisan migrate --seed
npm run build   # hoặc: npm run dev khi phát triển
php artisan storage:link
```

## Chạy ứng dụng (development)

```bash
php artisan serve
npm run dev
php artisan queue:work   # xử lý email/notification hàng đợi
```

Storefront: `http://localhost:8000`
Trang quản trị: `http://localhost:8000/admin`

## Biến môi trường quan trọng

| Biến | Mô tả |
| --- | --- |
| `APP_URL` | Domain chính thức của ứng dụng. Được dùng để sinh URL tuyệt đối (link đặt lại mật khẩu, xác minh email...) và để cấu hình `trustHosts`, tránh tấn công Host header. |
| `APP_TIMEZONE`, `APP_LOCALE`, `APP_FALLBACK_LOCALE` | Múi giờ và ngôn ngữ mặc định (`Asia/Ho_Chi_Minh`, `vi`). |
| `TRUSTED_PROXIES` | Danh sách IP/CIDR của reverse proxy được tin cậy (để dấu `*` để tin cậy tất cả, chỉ dùng sau load balancer nội bộ). |
| `SESSION_SECURE_COOKIE` | Đặt `true` khi chạy HTTPS ở production để cookie session chỉ gửi qua kết nối an toàn. |
| `ADMIN_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD` | Tài khoản Super Admin khởi tạo (được `AdminSeeder` tạo một lần, không ghi đè nếu email đã tồn tại). |
| `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` | Cấu hình đăng nhập Google cho khách hàng. |
| `BANK_TRANSFER_*` | Thông tin tài khoản nhận chuyển khoản hiển thị ở trang thanh toán. |
| `ORDER_UNPAID_EXPIRE_MINUTES`, `BANK_TRANSFER_EXPIRE_HOURS`, `ORDER_LOW_STOCK_THRESHOLD` | Cấu hình vòng đời đơn hàng và cảnh báo tồn kho thấp. |
| `VNPAY_*`, `MOMO_*` | Cấu hình cổng thanh toán trực tuyến. |
| `DEBUGBAR_ENABLED` | Bật/tắt Laravel Debugbar (chỉ nên bật ở local). |

Xem đầy đủ danh sách biến môi trường mẫu tại [`.env.example`](.env.example).

## Vai trò & phân quyền

- **Khách hàng** (`users`, guard `user`): trạng thái tài khoản `active`/`inactive`/`locked` (`UserConst::STATUS_*`). Tài khoản không active sẽ bị đăng xuất tự động (`EnsureUserIsActive`) và không đăng nhập được bằng mật khẩu lẫn Google.
- **Quản trị viên** (`admins`, guard `admin`): có `role` và bộ quyền (permission) chi tiết theo từng chức năng (đơn hàng, sản phẩm, khách hàng, đánh giá, câu hỏi, mã giảm giá...). Super Admin luôn có toàn quyền. Quyền được quản lý tại **Quản trị > Vai trò**.
- Toàn bộ route quản trị yêu cầu đăng nhập (`auth:admin`), tài khoản đang hoạt động (`admin.active`) và đúng quyền (`can:<ability>`).

## Bảo mật

- Giới hạn tần suất (rate limit) riêng cho đăng nhập, đăng ký, quên/đặt lại mật khẩu, xác minh email (khách hàng và quản trị viên tách biệt).
- Header bảo mật (`X-Frame-Options`, `Content-Security-Policy`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`, `Strict-Transport-Security` khi HTTPS) áp dụng cho mọi response.
- Đổi/đặt lại mật khẩu sẽ huỷ toàn bộ phiên đăng nhập khác của tài khoản đó.
- Trang quên mật khẩu luôn trả về thông báo trung lập, không tiết lộ email có tồn tại trong hệ thống hay không.
- Đăng nhập Google chỉ tự liên kết với tài khoản đã có khi email đã xác minh ở cả hai phía; ngược lại yêu cầu đăng nhập bằng mật khẩu trước để liên kết thủ công.

## Kiểm thử

```bash
php artisan test
```

Chạy riêng nhóm kiểm thử xác thực & bảo mật:

```bash
php artisan test tests/Feature/Auth tests/Feature/Security
```

Test chạy trên SQLite trong bộ nhớ (cấu hình sẵn trong `phpunit.xml`), không ảnh hưởng tới database phát triển (`database/database.sqlite`).

## Checklist trước khi triển khai production

- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] `APP_URL` trỏ đúng domain HTTPS chính thức
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `TRUSTED_PROXIES` cấu hình đúng dải IP của load balancer/CDN
- [ ] Đặt `ADMIN_EMAIL` / `ADMIN_PASSWORD` mạnh trước lần seed đầu tiên
- [ ] Cấu hình `MAIL_*` thật (không dùng driver `log`)
- [ ] Chạy `php artisan migrate --force` (không chạy seeder demo ở production)
- [ ] Chạy `php artisan config:cache route:cache view:cache`
- [ ] Bật `php artisan queue:work` (hoặc supervisor) để xử lý email/notification
- [ ] Kiểm tra chứng chỉ HTTPS và các header bảo mật đã được trả về đúng
