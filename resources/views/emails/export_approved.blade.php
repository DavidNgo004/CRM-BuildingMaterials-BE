<!DOCTYPE html>
<html>
<head>
    <title>Xác nhận đơn hàng: {{ $export->code }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #2c3e50; text-align: center;">Xác nhận đơn hàng xuất kho</h2>
        <p>Kính gửi quý khách <strong>{{ $customer->name }}</strong>,</p>
        <p>Chúng tôi xin trân trọng thông báo đơn hàng có mã <strong>{{ $export->code }}</strong> của quý khách đã được duyệt và đang trong quá trình chuẩn bị xuất kho.</p>
        
        <div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-top: 20px;">
            <h3 style="margin-top: 0;">Chi tiết đơn hàng:</h3>
            <p style="margin: 5px 0;"><strong>Mã đơn:</strong> {{ $export->code }}</p>
            <p style="margin: 5px 0;"><strong>Tổng tiền:</strong> {{ number_format($export->total_price) }} VND</p>
            <p style="margin: 5px 0;"><strong>Chiết khấu:</strong> {{ number_format($export->discount_amount) }} VND</p>
            <p style="margin: 5px 0;"><strong>Tổng thanh toán:</strong> <span style="color: #e74c3c; font-weight: bold;">{{ number_format($export->grand_total) }} VND</span></p>
            <p style="margin: 5px 0;"><strong>Ghi chú:</strong> {{ $export->note ?? 'Không có' }}</p>
        </div>

        <h3 style="margin-top: 30px;">Danh sách sản phẩm:</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Sản phẩm</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">Số lượng</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Đơn giá</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details as $item)
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;">{{ $item->product->name ?? 'N/A' }}</td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">{{ $item->quantity }}</td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">{{ number_format($item->unit_price) }}</td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">{{ number_format($item->total_price) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p style="margin-top: 30px;">Cảm ơn quý khách đã tin tưởng và sử dụng dịch vụ của chúng tôi!</p>
        <p style="font-size: 0.9em; color: #7f8c8d; text-align: center; margin-top: 40px; border-top: 1px solid #ddd; padding-top: 20px;">
            Trân trọng,<br>
            <strong>Cửa hàng Vật Liệu Xây Dựng</strong>
        </p>
    </div>
</body>
</html>
