<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Báo Trạng Thái Đơn Hàng</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f7fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 650px;
            margin: 0 auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        .header {
            text-align: center;
            padding-bottom: 30px;
            border-bottom: 2px solid #f1f5f9;
            margin-bottom: 30px;
        }
        .logo {
            margin-bottom: 15px;
            font-size: 28px;
            font-weight: bold;
            color: #3b82f6;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .logo img {
            max-height: 40px;
            margin-left: 10px;
        }
        h2 {
            color: #1e3a8a;
            margin-top: 0;
            font-weight: 700;
            font-size: 24px;
        }
        .greeting {
            font-size: 20px;
            margin-bottom: 25px;
        }
        .order-info {
            background-color: #f0f9ff;
            padding: 25px;
            border-radius: 12px;
            margin: 25px 0;
            border-left: 5px solid #3b82f6;
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.1);
        }
        .status {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(145deg, #4ade80 0%, #22c55e 100%);
            color: white;
            border-radius: 50px;
            font-weight: bold;
            margin: 12px 0;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .icon {
            vertical-align: middle;
            margin-right: 12px;
            font-size: 22px;
        }
        .info-row {
            margin-bottom: 12px;
            font-size: 16px;
        }
        .thank-you {
            font-size: 17px;
            line-height: 1.7;
            margin: 30px 0;
        }
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        .button {
            background: linear-gradient(145deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 14px 30px;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 15px rgba(37, 99, 235, 0.25);
        }
        .button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #f1f5f9;
            color: #64748b;
            font-size: 15px;
        }
        .social-icons {
            margin: 25px 0;
        }
        .social-icons a {
            display: inline-block;
            margin: 0 12px;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .social-icons a:hover {
            color: #1e3a8a;
        }
        .contact-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 20px 0;
        }
        .contact-item {
            display: flex;
            align-items: center;
        }
        .copyright {
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                🛍️ <img src="{{ asset('icon/duccomputer.png') }}" alt="Duc Computer Logo">
            </div>
        </div>
        
        <h2 class="greeting">👋 Xin chào {{ $order->customer->name }},</h2>
        
        <p>Chúng tôi xin thông báo đơn hàng của bạn đã được cập nhật trạng thái mới.</p>
        
        <div class="order-info">
            <p class="info-row"><span class="icon">📦</span> <strong>Mã đơn hàng:</strong> #{{ $order->id }}</p>
            <p class="info-row"><span class="icon">⏱️</span> <strong>Trạng thái hiện tại:</strong></p>
            <div class="status">
                ✅ {{ strtoupper($order->status) }}
            </div>
        </div>
        
        <p class="thank-you">Cảm ơn bạn đã tin tưởng và mua sắm tại Duc Computer! Chúng tôi sẽ tiếp tục cập nhật thông tin đơn hàng đến bạn trong thời gian sớm nhất.</p>
        
        <div class="button-container">
            <a href="#" class="button">
                <span class="icon">🔍</span> Kiểm tra đơn hàng
            </a>
        </div>
        
        <div class="footer">
            <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với đội ngũ hỗ trợ khách hàng của chúng tôi.</p>
            
            <div class="contact-info">
                <div class="contact-item">
                    <span class="icon">📞</span> 0386413805
                </div>
                <div class="contact-item">
                    <span class="icon">📧</span> duclvm.23itb@vku.udn.vn
                </div>
            </div>
            
            <div class="social-icons">
                <a href="#"><span class="icon">📘</span> Facebook</a>
                <a href="#"><span class="icon">📸</span> Instagram</a>
                <a href="#"><span class="icon">🐦</span> Twitter</a>
            </div>
            
            <p class="copyright">© 2025 Duc Computer. Tất cả các quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>