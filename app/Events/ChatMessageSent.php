<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $sender;
    public $timestamp;
    public $customer_id;

    public function __construct($message, $sender = 'Customer')
    {
        $this->message = $message;
        $this->sender = $sender;
        $this->timestamp = now()->toDateTimeString();
        Log::info("📢 Sự kiện `ChatMessageSent` đã được gọi.", [
            'message' => $this->message,
            'sender' => $this->sender
        ]);
    }

    public function broadcastOn()
    {
        Log::info("📢 Phát sự kiện vào kênh:", ['channel' => 'chat.message']);
        return new Channel('chat.message'); // Giữ nguyên Public Channel
    }
    
    public function broadcastAs()
    {
        return 'ChatMessageSent';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'sender' => $this->sender,
            'timestamp' => $this->timestamp,
        ];
    }
}
