<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Thêm broadcasting

    'allowed_methods' => ['*'],  // Cho phép tất cả các phương thức HTTP

    'allowed_origins' => ['*'],  // Thay thế bằng URL của frontend nếu cần bảo mật cao hơn

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],  // Cho phép tất cả các headers

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // Thay đổi thành true nếu cần hỗ trợ session hoặc cookie

];

