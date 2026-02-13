<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Device;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with optional status filter.
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'device.manufacturer'])
            ->orderByDesc('created_at');

        if ($request->filled('status') && array_key_exists($request->status, Order::STATUS_LABELS)) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(25)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $customers = Customer::orderBy('surname')->orderBy('name')->get();
        $selectedDevice = null;
        if (old('device_id')) {
            $selectedDevice = Device::with('manufacturer')->find(old('device_id'));
        }

        return view('orders.create', compact('customers', 'selectedDevice'));
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'device_id' => ['nullable', 'integer', 'exists:devices,id'],
            'cell_nr' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:5000'],
            'status' => ['nullable', 'string', 'in:to_be_ordered,ordered,received,back_order,cancelled'],
        ]);

        $validated['status'] = $validated['status'] ?? Order::STATUS_TO_BE_ORDERED;
        if (array_key_exists('device_id', $validated) && (string) $validated['device_id'] === '') {
            $validated['device_id'] = null;
        }

        Order::create($validated);

        return redirect()->route('orders.index')
            ->with('success', 'Order created successfully.');
    }

    /**
     * Search devices for the phone/device autocomplete (JSON).
     */
    public function searchDevices(Request $request)
    {
        $q = $request->get('q', '');
        $q = trim($q);

        if (strlen($q) < 2) {
            return response()->json(['devices' => []]);
        }

        $devices = Device::with('manufacturer')
            ->where(function ($query) use ($q) {
                $query->where('product_code', 'like', '%' . $q . '%')
                    ->orWhere('bar_code', 'like', '%' . $q . '%')
                    ->orWhere('model', 'like', '%' . $q . '%')
                    ->orWhereHas('manufacturer', function ($mq) use ($q) {
                        $mq->where('name', 'like', '%' . $q . '%');
                    });
            })
            ->limit(20)
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'label' => $d->product_code . ' - ' . ($d->manufacturer ? $d->manufacturer->name : '') . ' ' . $d->model,
                    'product_code' => $d->product_code,
                    'model' => $d->model,
                    'manufacturer' => $d->manufacturer ? $d->manufacturer->name : null,
                ];
            });

        return response()->json(['devices' => $devices]);
    }

    /**
     * Update an order's status (for Order / Back order / Cancel actions).
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:to_be_ordered,ordered,received,back_order,cancelled'],
        ]);

        $order->update(['status' => $validated['status']]);

        $label = Order::STATUS_LABELS[$validated['status']] ?? $validated['status'];

        return redirect()->route('orders.index', request()->only('status'))
            ->with('success', 'Order #' . $order->id . ' set to ' . $label . '.');
    }
}
