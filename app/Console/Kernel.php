<?php

namespace App\Console;

use App\Http\Controllers\Admin\StockController;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $timeThreshold = Carbon::now()->subMinutes(15);

            $ordersCount = Order::where('payment_status', 'unpaid')
                ->where('payment_method', 'online')
                ->where('created_at', '<', $timeThreshold)
                ->count();

            Log::info("Số lượng đơn hàng cần xóa: " . $ordersCount);

            if ($ordersCount > 0) {
                DB::transaction(function () use ($timeThreshold) {
                    Order::where('payment_status', 'unpaid')
                        ->where('payment_method', 'online')
                        ->where('created_at', '<', $timeThreshold)
                        ->forceDelete();
                });
                Log::info("Đã xóa đơn hàng thành công!");
            }
        })->everyMinute();

        // Kiểm tra hàng tồn kho lúc 08:00 sáng
        $schedule->call(function() {
            app(StockController::class)->checkLowStock();
        })->dailyAt("08:00");
    }


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
// php artisan schedule:run