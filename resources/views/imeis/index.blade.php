@extends('layouts.app')

@section('title', 'All IMEIs - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1>All IMEIs</h1>
            <p style="color: #64748b; font-size: 14px; margin-top: 4px;">Total: {{ $imeis->total() }} IMEIs</p>
        </div>
    </div>

    <div class="card" style="overflow-x: auto;">
        @if($imeis->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Invoice Nr</th>
                        <th>Phone</th>
                        <th>IMEI</th>
                        <th>Price</th>
                        <th>Allocated To</th>
                        <th>Name</th>
                        <th>Number</th>
                        <th>Deal Sheet Nr</th>
                        <th>Claim Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($imeis as $imei)
                        <tr>
                            <td>{{ $imei->date ? $imei->date->format('Y-m-d') : '-' }}</td>
                            <td>{{ $imei->invoice ?? '-' }}</td>
                            <td>{{ $imei->phone ?? '-' }}</td>
                            <td>
                                @if($imei->imei)
                                    <span class="imei-code">{{ $imei->imei }}</span>
                                @else
                                    <span class="badge badge-warning">Non-IMEI</span>
                                @endif
                            </td>
                            <td>
                                @if($imei->price)
                                    {{ config('app.currency_symbol') }} {{ number_format($imei->price, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-warning">Pending</span>
                            </td>
                            <td>{{ $imei->name ?? '-' }}</td>
                            <td>{{ $imei->number ?? '-' }}</td>
                            <td>{{ $imei->DealSheetNr ?? '-' }}</td>
                            <td>
                                <span class="badge badge-success">Active</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $imeis->links() }}</div>
        @else
            <div class="empty-state">
                <p>No IMEIs found in the database.</p>
            </div>
        @endif
    </div>
</div>
@endsection
