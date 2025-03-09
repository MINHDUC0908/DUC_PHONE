
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <title>Đặt Lại Mật Khẩu</title>
    <style>
        body {
            background-color: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .email-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            max-width: 500px;
            padding: 30px;
            text-align: center;
            margin: auto
        }
        .email-header {
            margin-bottom: 20px;
        }
        .reset-button {
            background-color: #edf0f3;
            border-radius: 6px;
            color: white;
            display: inline-block;
            margin: 20px 0;
            padding: 12px 24px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .reset-button:hover {
            background-color: #0056b3;
            color: black
        }
        .text-muted {
            color: #6c757d;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h2>Đặt Lại Mật Khẩu</h2>
        </div>
        <p>Bạn đã yêu cầu đặt lại mật khẩu. Nhấn vào nút dưới đây để tiếp tục:</p>
        
        <a href="{{ 'http://localhost:5173/forgot-password/'.$token }}" class="reset-button">
            Đặt Lại Mật Khẩu
        </a>
        
        <p class="text-muted">Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
        
        <hr>
        
        <p class="text-muted small">
            Nếu bạn gặp vấn đề, vui lòng liên hệ bộ phận hỗ trợ của chúng tôi.
        </p>
    </div>
</body>
</html>
