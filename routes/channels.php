<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// ✅ Thêm kênh chat.message vào đây
Broadcast::channel('chat.message', function ($user) {
    return true; // Nếu cần xác thực user, có thể thay đổi điều kiện ở đây
});
