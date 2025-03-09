<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LowStockAlert;
use App\Models\Color;
use Illuminate\Support\Facades\Mail;

class StockController extends Controller
{
    public function checkLowStock()
    {
        $lowStockItems = Color::where("quantity", "<=", 5)->get();
        if ($lowStockItems->isNotEmpty()) {
            $adminEmail = config('mail.admin_email', 'ducle090891999@gmail.com');
            Mail::to($adminEmail)->queue(new LowStockAlert($lowStockItems));
        }        
    }
}
