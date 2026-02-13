@extends('layouts.app')

@section('title', 'Repairs - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Repairs</h1>
        <a href="{{ route('repairs.create') }}" class="btn btn-primary">Add Repair</a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="card" style="overflow-x: auto;">
        @if($repairs->count() > 0)
            <table class="table" style="min-width: 900px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>IMEI</th>
                        <th>Status</th>
                        <th>Allocated To</th>
                        <th>Date Booked</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($repairs as $repair)
                        <tr onclick="window.location='{{ route('repairs.show', $repair) }}';">
                            <td>{{ $repair->id }}</td>
                            <td>
                                @if($repair->customer)
                                    <a href="{{ route('customers.show', $repair->customer) }}" onclick="event.stopPropagation();">{{ $repair->customer->name }} {{ $repair->customer->surname }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $repair->phone ?? '—' }}</td>
                            <td>{{ $repair->imei ?? '—' }}</td>
                            <td><span class="status-badge status-{{ $repair->ticket_status }}">{{ $repair->status_label }}</span></td>
                            <td>
                                @if($repair->allocatedTo)
                                    {{ $repair->allocatedTo->company ?: $repair->allocatedTo->name }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $repair->created_at->format('M j, Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $repairs->links() }}</div>
        @else
            <div class="empty-state">
                <p>No repair tickets yet.</p>
                <a href="{{ route('repairs.create') }}" class="btn btn-primary">Add Repair</a>
            </div>
        @endif
    </div>
</div>
@endsection
