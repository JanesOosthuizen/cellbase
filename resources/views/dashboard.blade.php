@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name'))

@push('styles')
<style>
.dash-welcome { padding: 24px 28px; }
.dash-welcome h1 { font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 4px; letter-spacing: -0.02em; }
.dash-welcome p { color: #64748b; font-size: 14px; }
.dash-stats { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 16px; margin-bottom: 28px; }
.dash-stat { background: var(--card-bg); border-radius: var(--card-radius); box-shadow: var(--card-shadow); padding: 18px; text-decoration: none; color: inherit; transition: box-shadow 0.15s, transform 0.15s; display: block; }
.dash-stat:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateY(-1px); }
.dash-stat__value { font-size: 28px; font-weight: 700; color: #0f172a; line-height: 1.2; letter-spacing: -0.02em; }
.dash-stat__label { font-size: 13px; color: #64748b; margin-top: 4px; }
.dash-stat__meta { font-size: 11px; color: #94a3b8; margin-top: 6px; }
.dash-section { margin-bottom: 28px; }
.dash-section h2 { font-size: 18px; font-weight: 600; color: #0f172a; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; }
.dash-repair-status { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; }
.dash-repair-status span { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; font-size: 13px; background: #f1f5f9; color: #475569; }
.dash-repair-status span strong { color: #0f172a; }
.dash-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.dash-table th { text-align: left; padding: 10px 14px; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border-bottom: 1px solid #e2e8f0; }
.dash-table td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; color: #334155; }
.dash-table tbody tr:hover { background: #f8fafc; }
.dash-table a { color: #0ea5e9; text-decoration: none; }
.dash-table a:hover { text-decoration: underline; }
.dash-table .status-badge { font-size: 11px; padding: 3px 8px; }
.dash-empty { color: #94a3b8; font-size: 14px; padding: 24px; text-align: center; }
.dash-grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 24px; }
.dash-account-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; }
.dash-account-card h3 { font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 10px; }
.dash-account-card p, .dash-account-card .badge-wrap { font-size: 13px; color: #64748b; }
.dash-account-card .badge-wrap { margin-top: 8px; }
.dash-account-card .badge-wrap span { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 500; margin-right: 6px; margin-bottom: 6px; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="card dash-welcome">
        <h1>Welcome, {{ Auth::user()->name }}</h1>
        <p>Here’s what’s going on in your workspace.</p>
    </div>

    <div class="dash-stats">
        <a href="{{ route('repairs.index') }}" class="dash-stat">
            <div class="dash-stat__value">{{ number_format($repairsTotal) }}</div>
            <div class="dash-stat__label">Repairs</div>
            @if($repairsByStatus->isNotEmpty())
                <div class="dash-stat__meta">{{ $repairsByStatus->get('booked_in', 0) }} booked in · {{ $repairsByStatus->get('collected', 0) }} collected</div>
            @endif
        </a>
        <a href="{{ route('customers.index') }}" class="dash-stat">
            <div class="dash-stat__value">{{ number_format($customersTotal) }}</div>
            <div class="dash-stat__label">Customers</div>
        </a>
        <a href="{{ route('orders.index') }}" class="dash-stat">
            <div class="dash-stat__value">{{ number_format($ordersTotal) }}</div>
            <div class="dash-stat__label">Orders</div>
            @if($ordersPending > 0)
                <div class="dash-stat__meta">{{ $ordersPending }} pending</div>
            @endif
        </a>
        <a href="{{ route('invoices.index') }}" class="dash-stat">
            <div class="dash-stat__value">{{ number_format($invoicesTotal) }}</div>
            <div class="dash-stat__label">Invoices</div>
            @if($invoicesDraft > 0)
                <div class="dash-stat__meta">{{ $invoicesDraft }} draft</div>
            @endif
        </a>
        <a href="{{ route('devices.index') }}" class="dash-stat">
            <div class="dash-stat__value">{{ number_format($devicesTotal) }}</div>
            <div class="dash-stat__label">Devices</div>
        </a>
        <a href="{{ route('imeis.index') }}" class="dash-stat">
            <div class="dash-stat__value">{{ number_format($imeisTotal) }}</div>
            <div class="dash-stat__label">IMEIs</div>
        </a>
    </div>

    <div class="dash-grid-2">
        <div class="card dash-section">
            <h2>Recent Repairs</h2>
            @if($recentRepairs->isNotEmpty())
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentRepairs as $repair)
                            <tr>
                                <td><a href="{{ route('repairs.show', $repair) }}">#{{ $repair->id }}</a></td>
                                <td>
                                    @if($repair->customer)
                                        <a href="{{ route('customers.show', $repair->customer) }}" onclick="event.stopPropagation();">{{ trim($repair->customer->name . ' ' . $repair->customer->surname) ?: $repair->customer->company_name ?: '—' }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td><span class="status-badge status-{{ $repair->ticket_status }}">{{ $repair->status_label }}</span></td>
                                <td>{{ $repair->created_at->format('M j, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p style="margin-top: 12px;"><a href="{{ route('repairs.index') }}" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">View all repairs</a></p>
            @else
                <div class="dash-empty">No repairs yet. <a href="{{ route('repairs.create') }}">Create one</a></div>
            @endif
        </div>

        <div class="card dash-section">
            <h2>Recent Orders</h2>
            @if($recentOrders->isNotEmpty())
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Device</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr>
                                <td><a href="{{ route('orders.index') }}">#{{ $order->id }}</a></td>
                                <td>
                                    @if($order->customer)
                                        <a href="{{ route('customers.show', $order->customer) }}">{{ trim($order->customer->name . ' ' . $order->customer->surname) ?: $order->customer->company_name ?: '—' }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $order->device ? ($order->device->manufacturer ? $order->device->manufacturer->name . ' ' : '') . $order->device->model : '—' }}</td>
                                <td><span class="status-badge status-{{ $order->status }}">{{ $order->status_label }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p style="margin-top: 12px;"><a href="{{ route('orders.index') }}" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">View all orders</a></p>
            @else
                <div class="dash-empty">No orders yet. <a href="{{ route('orders.create') }}">Create one</a></div>
            @endif
        </div>
    </div>

    <div class="dash-section">
        <h2 style="border: none; padding: 0; margin-bottom: 16px;">Your account</h2>
        <div class="dash-account-grid">
            <div class="card dash-account-card">
                <h3>Profile</h3>
                <p><strong>Email</strong> {{ Auth::user()->email }}</p>
                <p style="margin-top: 6px;"><strong>Member since</strong> {{ Auth::user()->created_at->format('F j, Y') }}</p>
            </div>
            <div class="card dash-account-card">
                <h3>Roles</h3>
                @if(Auth::user()->roles->count() > 0)
                    <div class="badge-wrap">
                        @foreach(Auth::user()->roles as $role)
                            <span style="background: #e0f2fe; color: #0369a1;">{{ ucwords(str_replace('-', ' ', $role->name)) }}</span>
                        @endforeach
                    </div>
                @else
                    <p>No roles assigned</p>
                @endif
            </div>
            <div class="card dash-account-card">
                <h3>Permissions</h3>
                @php $permissions = Auth::user()->roles->flatMap->permissions->unique('id'); @endphp
                @if($permissions->count() > 0)
                    <div class="badge-wrap">
                        @foreach($permissions->take(10) as $permission)
                            <span style="background: #d1fae5; color: #047857;">{{ ucwords(str_replace('-', ' ', $permission->name)) }}</span>
                        @endforeach
                        @if($permissions->count() > 10)
                            <span style="background: #f1f5f9; color: #64748b;">+{{ $permissions->count() - 10 }} more</span>
                        @endif
                    </div>
                @else
                    <p>No permissions assigned</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
