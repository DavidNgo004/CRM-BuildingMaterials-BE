<!DOCTYPE html>
<html>
<head>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Kính gửi đối tác {{ $supplier->name }},</h2>
    <p>Chúng tôi xin gửi đến quý công ty Đơn Đặt Hàng mang mã số: <strong>{{ $import->code }}</strong>.</p>
    <p>Chi tiết các mặt hàng chúng tôi cần nhập như sau:</p>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên sản phẩm</th>
                <th>Đơn vị tính</th>
                <th>Số lượng đặt</th>
                <th>Ghi chú khác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['product']->name }}</td>
                <td>{{ $item['product']->unit }}</td>
                <td><strong>{{ $item['quantity'] }}</strong></td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p>Xin vui lòng phản hồi và sắp xếp lịch giao hàng sớm nhất.</p>
    <p>Trân trọng,<br>Ban Quản Lý Kho</p>
</body>
</html>
