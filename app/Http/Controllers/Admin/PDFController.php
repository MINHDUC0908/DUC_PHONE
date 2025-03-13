<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PDFController extends Controller
{
    public function print($id)
    {
        $order = Order::with('orderItems')->findOrFail($id);
        
        // Đọc file ảnh và chuyển sang base64
        $path = public_path('icon/image.png');
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64Image = 'data:image/' . $type . ';base64,' . base64_encode($data);
        } else {
            $base64Image = null;
        }
    
        // Truyền dữ liệu qua view
        $pdf = Pdf::loadView('admin.order.print', compact('order', 'base64Image'))
                  ->setPaper('A4')
                  ->setOptions(['defaultFont' => 'DejaVu Sans']);
        return $pdf->stream('order-'.$id.'.pdf');
    }
}
