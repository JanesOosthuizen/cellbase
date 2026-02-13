@extends('layouts.app')

@section('title', 'Loan Devices - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Loan Devices</h1>
        <a href="{{ route('loan-devices.create') }}" class="btn btn-primary">Add Loan Device</a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="card" style="overflow-x: auto;">
        @if($loanDevices->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Phone (Device)</th>
                        <th>IMEI</th>
                        <th>Allocated to (Customer)</th>
                        <th>Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loanDevices as $loan)
                        <tr>
                            <td>
                                {{ $loan->device->product_code ?? '' }}
                                {{ $loan->device->manufacturer ? $loan->device->manufacturer->name . ' ' : '' }}
                                {{ $loan->device->model }}
                            </td>
                            <td><span class="imei-code">{{ $loan->imei ?? '—' }}</span></td>
                            <td>
                                @if($loan->repair && $loan->repair->customer)
                                    <a href="{{ route('customers.show', $loan->repair->customer) }}">{{ trim($loan->repair->customer->name . ' ' . $loan->repair->customer->surname) ?: $loan->repair->customer->company_name }}</a>
                                    <span style="color: #94a3b8; font-size: 12px;">(Repair #{{ $loan->repair->id }})</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $loan->created_at->format('M j, Y') }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('loan-devices.edit', $loan) }}" class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;">Edit</a>
                                    <form method="POST" action="{{ route('loan-devices.destroy', $loan) }}" style="display: inline;" onsubmit="return confirm('Remove this loan device?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="padding: 8px 16px; font-size: 14px;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $loanDevices->links() }}</div>
        @else
            <div class="empty-state">
                <p>No loan devices yet. Add a device to track as a loan phone with its IMEI.</p>
                <a href="{{ route('loan-devices.create') }}" class="btn btn-primary">Add Loan Device</a>
            </div>
        @endif
    </div>
</div>
@endsection
