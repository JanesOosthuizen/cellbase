<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Device;
use App\Models\Imei;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Repair;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $repairsTotal = Repair::count();
        $repairsByStatus = Repair::query()
            ->selectRaw('ticket_status, count(*) as count')
            ->groupBy('ticket_status')
            ->pluck('count', 'ticket_status');

        $customersTotal = Customer::count();
        $ordersTotal = Order::count();
        $ordersPending = Order::whereIn('status', [
            Order::STATUS_TO_BE_ORDERED,
            Order::STATUS_ORDERED,
            Order::STATUS_BACK_ORDER,
        ])->count();
        $invoicesTotal = Invoice::count();
        $invoicesDraft = Invoice::where('status', 'draft')->count();
        $devicesTotal = Device::count();
        $imeisTotal = Imei::count();

        $recentRepairs = Repair::with('customer')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $recentOrders = Order::with(['customer', 'device.manufacturer'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'repairsTotal' => $repairsTotal,
            'repairsByStatus' => $repairsByStatus,
            'customersTotal' => $customersTotal,
            'ordersTotal' => $ordersTotal,
            'ordersPending' => $ordersPending,
            'invoicesTotal' => $invoicesTotal,
            'invoicesDraft' => $invoicesDraft,
            'devicesTotal' => $devicesTotal,
            'imeisTotal' => $imeisTotal,
            'recentRepairs' => $recentRepairs,
            'recentOrders' => $recentOrders,
        ]);
    }
}
