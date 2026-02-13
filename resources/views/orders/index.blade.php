@extends('layouts.app')

@section('title', 'Orders - ' . config('app.name'))

@push('styles')
<style>
.order-filters { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin-bottom: 20px; }
.order-filters a { padding: 8px 14px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; background: #f1f5f9; color: #475569; transition: all 0.15s; }
.order-filters a:hover { background: #e2e8f0; color: #0f172a; }
.order-filters a.active { background: #0ea5e9; color: #fff; }
.order-filters span { font-size: 14px; color: #64748b; margin-right: 8px; }
.status-to_be_ordered { background: #e0f2fe; color: #0369a1; }
.status-ordered { background: #fef3c7; color: #92400e; }
.status-received { background: #d1fae5; color: #065f46; }
.status-back_order { background: #fce7f3; color: #9d174d; }
.status-cancelled { background: #f1f5f9; color: #64748b; }
.order-actions { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
.order-actions form { display: inline; }
.order-actions .btn-sm { padding: 6px 12px; font-size: 13px; }
</style>
@endpush

@section('content')
<div class="container" style="max-width: 100%;">
    <div class="page-header">
        <h1>Orders</h1>
        <a href="{{ route('orders.create') }}" class="btn btn-primary">Add Order</a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="order-filters">
        <span>Filter by status:</span>
        <a href="{{ route('orders.index') }}" class="{{ !request('status') ? 'active' : '' }}">All</a>
        @foreach(\App\Models\Order::STATUS_LABELS as $value => $label)
            <a href="{{ route('orders.index', ['status' => $value]) }}" class="{{ request('status') === $value ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="card" style="overflow-x: auto;">
        @if($orders->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Phone (Device)</th>
                        <th>Cell Nr</th>
                        <th>Note</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>
                                @if($order->customer)
                                    <a href="{{ route('customers.show', $order->customer) }}">{{ $order->customer->name }} {{ $order->customer->surname }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($order->device)
                                    {{ $order->device->product_code }} {{ $order->device->manufacturer ? $order->device->manufacturer->name : '' }} {{ $order->device->model }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $order->cell_nr ?? '—' }}</td>
                            <td style="max-width: 200px;">{{ \Str::limit($order->note, 50) ?? '—' }}</td>
                            <td><span class="status-badge status-{{ $order->status }}">{{ $order->status_label }}</span></td>
                            <td>{{ $order->created_at->format('M j, Y H:i') }}</td>
                            <td>
                                <div class="order-actions">
                                    @if($order->status === \App\Models\Order::STATUS_TO_BE_ORDERED)
                                        <form method="POST" action="{{ route('orders.update-status', $order) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="ordered">
                                            <button type="submit" class="btn btn-primary btn-sm">Order</button>
                                        </form>
                                    @endif
                                    @if($order->status === \App\Models\Order::STATUS_ORDERED)
                                        <form method="POST" action="{{ route('orders.update-status', $order) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="back_order">
                                            <button type="submit" class="btn btn-secondary btn-sm">Back order</button>
                                        </form>
                                    @endif
                                    @if($order->status !== \App\Models\Order::STATUS_CANCELLED)
                                        <form method="POST" action="{{ route('orders.update-status', $order) }}" style="display: inline;" onsubmit="return confirm('Mark this order as cancelled?');">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('orders.update-status', $order) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="to_be_ordered">
                                            <button type="submit" class="btn btn-secondary btn-sm">Restore</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $orders->links() }}</div>
        @else
            <div class="empty-state">
                <p>No orders found. @if(request('status'))Try changing the status filter. @else Create your first order. @endif</p>
                <a href="{{ route('orders.create') }}" class="btn btn-primary">Add Order</a>
            </div>
        @endif
    </div>
</div>
@endsection
