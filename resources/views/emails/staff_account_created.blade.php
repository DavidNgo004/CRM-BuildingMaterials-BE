<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin tài khoản nhân viên</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f0f4f8; font-family: 'Segoe UI', Arial, sans-serif;">
    <div style="max-width: 620px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">

        {{-- Header --}}
        <div style="background: linear-gradient(135deg, #1a237e 0%, #283593 60%, #3949ab 100%); padding: 40px 40px 30px; text-align: center;">
            <div style="width: 64px; height: 64px; background: rgba(255,255,255,0.15); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                <span style="font-size: 32px;">👤</span>
            </div>
            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: 0.3px;">
                Tài Khoản Nhân Viên Đã Được Tạo
            </h1>
            <p style="margin: 8px 0 0; color: rgba(255,255,255,0.8); font-size: 14px;">
                Hệ thống CRM – Cửa hàng Vật Liệu Xây Dựng
            </p>
        </div>

        {{-- Body --}}
        <div style="padding: 36px 40px;">
            <p style="margin: 0 0 16px; color: #37474f; font-size: 15px; line-height: 1.7;">
                Xin chào <strong style="color: #1a237e;">{{ $staffName }}</strong>,
            </p>
            <p style="margin: 0 0 28px; color: #546e7a; font-size: 14px; line-height: 1.8;">
                Tài khoản nhân viên của bạn trên hệ thống quản lý kho CRM VLXD đã được tạo thành công.
                Vui lòng sử dụng thông tin dưới đây để đăng nhập.
            </p>

            {{-- Account Info Card --}}
            <div style="background: linear-gradient(135deg, #e8eaf6 0%, #f3f4fd 100%); border-left: 4px solid #3949ab; border-radius: 8px; padding: 24px 28px; margin-bottom: 28px;">
                <h3 style="margin: 0 0 16px; color: #1a237e; font-size: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                    🔐 Thông Tin Đăng Nhập
                </h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; color: #78909c; font-size: 13px; font-weight: 600; width: 130px; vertical-align: top;">Email:</td>
                        <td style="padding: 8px 0; color: #1a237e; font-size: 14px; font-weight: 700;">{{ $staffEmail }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #78909c; font-size: 13px; font-weight: 600; vertical-align: top;">Mật khẩu:</td>
                        <td style="padding: 8px 0;">
                            <span style="background-color: #ffffff; border: 1.5px dashed #3949ab; border-radius: 6px; padding: 4px 12px; color: #1a237e; font-size: 15px; font-weight: 700; letter-spacing: 1px; font-family: 'Courier New', monospace;">
                                {{ $plainPassword }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #78909c; font-size: 13px; font-weight: 600; vertical-align: top;">Vai trò:</td>
                        <td style="padding: 8px 0;">
                            <span style="background-color: #c5cae9; color: #1a237e; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 20px; text-transform: uppercase;">
                                Nhân viên kho
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- Warning --}}
            <div style="background-color: #fff8e1; border-left: 4px solid #ffc107; border-radius: 8px; padding: 16px 20px; margin-bottom: 28px;">
                <p style="margin: 0; color: #795548; font-size: 13px; line-height: 1.7;">
                    ⚠️ <strong>Lưu ý bảo mật:</strong> Đây là mật khẩu khởi tạo. Vui lòng đăng nhập và
                    <strong>đổi mật khẩu ngay</strong> để bảo vệ tài khoản của bạn.
                    Không chia sẻ thông tin này với bất kỳ ai.
                </p>
            </div>

            {{-- CTA Button --}}
            <div style="text-align: center; margin-bottom: 32px;">
                <a href="{{ config('app.url') }}"
                   style="display: inline-block; background: linear-gradient(135deg, #283593 0%, #3949ab 100%); color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 8px; font-size: 15px; font-weight: 700; letter-spacing: 0.3px; box-shadow: 0 4px 12px rgba(57,73,171,0.35);">
                    🚀 Đăng Nhập Ngay
                </a>
            </div>

            <p style="margin: 0; color: #90a4ae; font-size: 13px; line-height: 1.7; text-align: center;">
                Nếu bạn gặp bất kỳ vấn đề nào, vui lòng liên hệ quản trị viên hệ thống để được hỗ trợ.
            </p>
        </div>

        {{-- Footer --}}
        <div style="background-color: #eceff1; padding: 20px 40px; text-align: center; border-top: 1px solid #e0e0e0;">
            <p style="margin: 0; color: #90a4ae; font-size: 12px; line-height: 1.6;">
                Email này được gửi tự động từ hệ thống CRM VLXD. Vui lòng không trả lời email này.<br>
                <strong style="color: #78909c;">© {{ date('Y') }} Cửa hàng Vật Liệu Xây Dựng</strong>
            </p>
        </div>

    </div>
</body>
</html>
