<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cảnh báo: Sản phẩm sắp hết hàng</title>
    <style>
        /* CSS cơ bản cho email */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
        }
        h1 {
            color: #d9534f;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table th {
            background-color: #f0f0f0;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Cảnh báo: Sản phẩm sắp hết hàng</h1>
        <p>Xin chào,</p>
        <p>Dưới đây là danh sách các sản phẩm có số lượng tồn kho thấp:</p>
        
        <table>
            <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng tồn</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($colors as $color)
                    <tr>
                        <td>{{ $color->products ? $color->products->product_name : 'Không xác định' }}</td>
                        <td>{{ $color->quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p>Vui lòng kiểm tra và cập nhật kho hàng nếu cần thiết.</p>
        <p>Trân trọng,</p>
        <p>Team Quản lý</p>
    </div>
    <div class="footer">
        © {{ date('Y') }} Your Company Name. All rights reserved.
    </div>
</body>
</html>
