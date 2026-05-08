<!DOCTYPE html>
<html>

<head>
    <title>Thông báo trạng thái tài khoản</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #6366f1; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
            Thông báo trạng thái tài khoản
        </h2>

        <p>Xin chào <strong>{{ $staffName }}</strong>,</p>

        <p>Tài khoản nhân viên của bạn trên hệ thống CRM VLXD vừa được cập nhật trạng thái.</p>

        <div style="background: #f9fafb; padding: 15px; border-radius: 6px; margin: 20px 0;">
            <p style="margin: 0;">Trạng thái hiện tại:</p>
            @if ($isLocked)
                <h3 style="color: #ef4444; margin: 10px 0;">Đã bị khoá </h3>
                <p style="margin: 0; color: #6b7280; font-size: 14px;">Bạn sẽ không thể đăng nhập vào hệ thống.</p>
            @else
                <h3 style="color: #10b981; margin: 10px 0;">Đang hoạt động </h3>
                <p style="margin: 0; color: #6b7280; font-size: 14px;">Bạn có thể đăng nhập vào hệ thống bình thường.</p>
            @endif
        </div>

        <p>Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ trực tiếp với quản trị viên (Admin) để được giải đáp.</p>

        <p style="margin-top: 30px; font-size: 14px; color: #6b7280;">
            Trân trọng,<br>
            <strong>Ban quản trị CRM VLXD</strong>
        </p>
    </div>
</body>

</html>