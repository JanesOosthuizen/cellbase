@extends('layouts.app')

@section('title', 'Invoices - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Invoices</h1>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">Add Invoice</a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="card" style="overflow-x: auto;">
        @if($invoices->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Invoice Nr</th>
                        <th>Invoice Date</th>
                        <th>Total (Excl)</th>
                        <th>Lines</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_nr }}</td>
                            <td>{{ $invoice->invoice_date->format('M j, Y') }}</td>
                            <td class="text-right">${{ number_format($invoice->invoice_total_excl, 2) }}</td>
                            <td>{{ $invoice->invoiceLines->count() }}</td>
                            <td>
                                <span class="badge badge-{{ $invoice->status }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td>{{ $invoice->created_at->format('M j, Y') }}</td>
                            <td>
                                @if($invoice->status === 'draft')
                                    <form method="POST" action="{{ route('invoices.approve', $invoice) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to approve this invoice? This will create IMEI records and cannot be undone.');">
                                        @csrf
                                        <button type="submit" class="btn btn-primary" style="padding: 6px 12px; font-size: 13px;">Approve</button>
                                    </form>
                                @else
                                    <span style="color: #64748b; font-size: 13px;">Approved</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $invoices->links() }}</div>
        @else
            <div class="empty-state">
                <p>No invoices found.</p>
                <a href="{{ route('invoices.create') }}" class="btn btn-primary">Add Your First Invoice</a>
            </div>
        @endif
    </div>
</div>
@endsection
