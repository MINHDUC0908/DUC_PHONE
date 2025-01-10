<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $sender;
    // public $customer_id;
    public $timestamp;

    public function __construct($message, $sender = 'Customer',)
    {
        $this->message = $message;
        $this->sender = $sender;
        // $this->customer_id = $customer_id;
        $this->timestamp = now()->toDateTimeString();
    }

    public function broadcastOn()
    {
        return new Channel('chat');
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
            // 'customer_id' => $this->customer_id,
            'timestamp' => $this->timestamp
        ];
    }
}
