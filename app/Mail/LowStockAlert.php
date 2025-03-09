<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockAlert extends Mailable
{
    use Queueable, SerializesModels;
    public $colors;
    public function __construct($colors)
    {
        $this->colors = $colors;
    }
    public function build()
    {
        return $this->subject("Cảnh báo sản phẩm sắp hết hàng!!!")
                    ->view("emails.lowStock")
                    ->with(['colors' => $this->colors]); // Truyền dữ liệu xuống view
    }    
}
