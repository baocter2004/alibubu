<?php

return [
    'login' => [
        'title' => 'Đăng nhập',
        'heading' => 'Chào mừng trở lại',
        'subheading' => 'Nhập thông tin để truy cập tài khoản của bạn.',
        'email' => 'Email',
        'password' => 'Mật khẩu',
        'remember' => 'Ghi nhớ đăng nhập',
        'forgot' => 'Quên mật khẩu?',
        'submit' => 'Đăng nhập',
        'no_account' => 'Chưa có tài khoản?',
        'register_link' => 'Đăng ký',
        'google' => 'Tiếp tục với Google',
    ],

    'register' => [
        'title' => 'Đăng ký',
        'heading' => 'Tạo tài khoản',
        'subheading' => 'Tham gia Alibubu và bắt đầu mua sắm ngay hôm nay.',
        'fullname' => 'Họ và tên',
        'email' => 'Email',
        'password' => 'Mật khẩu',
        'password_confirmation' => 'Xác nhận mật khẩu',
        'submit' => 'Đăng ký',
        'have_account' => 'Đã có tài khoản?',
        'login_link' => 'Đăng nhập',
    ],

    'forgot' => [
        'title' => 'Quên mật khẩu',
        'heading' => 'Quên mật khẩu?',
        'subheading' => 'Nhập email đã đăng ký, chúng tôi sẽ gửi liên kết đặt lại mật khẩu.',
        'submit' => 'Gửi liên kết đặt lại',
        'remembered' => 'Nhớ mật khẩu rồi?',
        'login_link' => 'Đăng nhập',
    ],

    'reset' => [
        'title' => 'Đặt lại mật khẩu',
        'heading' => 'Đặt lại mật khẩu',
        'subheading' => 'Nhập mật khẩu mới cho tài khoản của bạn.',
        'password' => 'Mật khẩu mới',
        'password_confirmation' => 'Xác nhận mật khẩu',
        'submit' => 'Đổi mật khẩu',
    ],

    'mail' => [
        'verify' => [
            'title' => 'Xác minh email',
            'greeting' => 'Xin chào :name,',
            'intro' => 'Nhấn nút bên dưới để xác minh tài khoản của bạn:',
            'action' => 'Xác minh email',
            'expires' => 'Liên kết sẽ hết hạn sau :minutes phút.',
            'ignore' => 'Nếu bạn không tạo tài khoản, hãy bỏ qua email này.',
        ],
        'password_changed' => [
            'subject' => 'Mật khẩu của bạn đã được thay đổi',
            'line' => 'Mật khẩu tài khoản Alibubu của bạn vừa được thay đổi lúc :time.',
            'warning' => 'Nếu không phải bạn thực hiện, vui lòng đặt lại mật khẩu ngay và liên hệ hỗ trợ.',
        ],
        'google_linked' => [
            'subject' => 'Tài khoản Google đã được liên kết',
            'line' => 'Tài khoản Google của bạn vừa được liên kết với tài khoản Alibubu này lúc :time.',
            'warning' => 'Nếu không phải bạn thực hiện, vui lòng đổi mật khẩu ngay.',
        ],
        'email_changed' => [
            'subject' => 'Email tài khoản của bạn đã được thay đổi',
            'line' => 'Email đăng nhập cho tài khoản Alibubu của bạn đã được đổi thành :new_email lúc :time.',
            'warning' => 'Nếu không phải bạn thực hiện, vui lòng liên hệ hỗ trợ ngay.',
        ],
    ],

    'messages' => [
        'registered' => 'Đăng ký thành công! Vui lòng xác nhận email để có thể mua hàng.',
        'register_failed' => 'Đăng ký thất bại. Vui lòng thử lại.',
        'logged_in' => 'Đăng nhập thành công!',
        'logged_out' => 'Đăng xuất thành công!',
        'login_failed' => 'Email hoặc mật khẩu không chính xác.',
        'reset_link_sent' => 'Nếu email tồn tại trong hệ thống, chúng tôi đã gửi liên kết đặt lại mật khẩu. Vui lòng kiểm tra hộp thư đến!',
        'reset_link_failed' => 'Không gửi được email đặt lại mật khẩu. Vui lòng thử lại sau.',
        'reset_success' => 'Đổi mật khẩu thành công. Vui lòng đăng nhập lại!',
        'reset_failed' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.',
        'google_failed' => 'Không thể đăng nhập bằng Google. Vui lòng thử lại.',
        'account_locked' => 'Tài khoản bị khoá hoặc không hợp lệ.',
        'account_inactive' => 'Tài khoản của bạn hiện không hoạt động. Vui lòng liên hệ hỗ trợ.',
        'account_locked_reason' => 'Tài khoản của bạn đã bị khoá: :reason',
        'throttled' => 'Bạn đã thử quá nhiều lần. Vui lòng thử lại sau :seconds giây.',
        'google_link_required' => 'Email :email đã được đăng ký. Vui lòng đăng nhập bằng mật khẩu để liên kết tài khoản Google.',
        'google_conflict' => 'Tài khoản này đã được liên kết với một tài khoản Google khác.',
        'google_unverified' => 'Email Google của bạn chưa được xác minh. Vui lòng xác minh email Google trước.',
        'email_already_verified' => 'Email đã được xác minh trước đó.',
        'verification_resent' => 'Đã gửi lại email xác minh. Vui lòng kiểm tra hộp thư.',
        'verification_resend_failed' => 'Gửi lại email xác minh thất bại. Vui lòng thử lại sau.',
        'must_login' => 'Bạn cần đăng nhập để tiếp tục.',
    ],
];
