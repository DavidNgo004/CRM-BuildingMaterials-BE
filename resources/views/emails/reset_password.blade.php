<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f6f9; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="color: #1890ff; text-align: center; margin-bottom: 20px;">YÊU CẦU ĐẶT LẠI MẬT KHẨU</h2>
        <p>Xin chào,</p>
        <p>Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn tại hệ thống CRM-VLXD.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}" style="background-color: #1890ff; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 4px; font-weight: bold; display: inline-block;">Đặt lại mật khẩu</a>
        </div>

        <p>Liên kết đặt lại mật khẩu này sẽ hết hạn trong 60 phút.</p>
        <p>Nếu bạn không yêu cầu đặt lại mật khẩu, xin vui lòng bỏ qua email này. Tài khoản của bạn vẫn an toàn.</p>
        
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #777; text-align: center;">
            <p>Trân trọng,<br>Ban quản trị CRM-VLXD</p>
            <p>Nếu bạn gặp vấn đề khi bấm vào nút "Đặt lại mật khẩu", hãy copy và dán URL sau vào trình duyệt web của bạn: <br>
            <a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
        </div>
    </div>
</body>
</html>
