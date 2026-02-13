@extends('layouts.app')

@section('title', 'Manage Devices - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Manage Devices</h1>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('devices.import') }}" class="btn btn-secondary">Import from Excel</a>
            <a href="{{ route('devices.create') }}" class="btn btn-primary">Add Device</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="card" style="overflow-x: auto;">
        @if($devices->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Product Code</th>
                        <th>Bar Code</th>
                        <th>Manufacturer</th>
                        <th>Model</th>
                        <th class="text-right">Cost Excl</th>
                        <th class="text-right">Cost Incl</th>
                        <th class="text-right">RSP Excl</th>
                        <th class="text-right">RSP Incl</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($devices as $device)
                        <tr>
                            <td>{{ $device->product_code }}</td>
                            <td>{{ $device->bar_code ?? '-' }}</td>
                            <td>{{ $device->manufacturer ? $device->manufacturer->name : '-' }}</td>
                            <td>{{ $device->model }}</td>
                            <td class="text-right">{{ $device->cost_excl ? '$' . number_format($device->cost_excl, 2) : '-' }}</td>
                            <td class="text-right">{{ $device->cost_incl ? '$' . number_format($device->cost_incl, 2) : '-' }}</td>
                            <td class="text-right">{{ $device->rsp_excl ? '$' . number_format($device->rsp_excl, 2) : '-' }}</td>
                            <td class="text-right">{{ $device->rsp_incl ? '$' . number_format($device->rsp_incl, 2) : '-' }}</td>
                            <td>{{ $device->created_at->format('M j, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $devices->links() }}</div>
        @else
            <div class="empty-state">
                <p>No devices found.</p>
                <a href="{{ route('devices.create') }}" class="btn btn-primary">Add Your First Device</a>
            </div>
        @endif
    </div>
</div>
@endsection
