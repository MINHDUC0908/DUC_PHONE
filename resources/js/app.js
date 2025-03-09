import './bootstrap';

// Lắng nghe tin nhắn mới
window.Echo.private("chat.message")
    .listen(".ChatMessageSent", (data) => {
        alert(`📩 Tin nhắn mới từ ${data.sender}: ${data.message}`);
    });