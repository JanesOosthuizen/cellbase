@extends('layouts.app')

@section('title', ($customer->name ?? 'Customer') . ' ' . ($customer->surname ?? '') . ' - ' . config('app.name'))

@push('styles')
<style>
.back-link { display: inline-flex; align-items: center; gap: 8px; color: #64748b; text-decoration: none; font-size: 14px; font-weight: 500; margin-bottom: 24px; }
.back-link:hover { color: #0ea5e9; }
.detail-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px 32px; }
.detail-item label { display: block; font-size: 12px; font-weight: 500; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
.detail-item .value { font-size: 15px; color: #334155; }
.detail-item .value.empty { color: #94a3b8; font-style: italic; }
.tabs { display: flex; gap: 4px; border-bottom: 2px solid #e2e8f0; margin-bottom: 0; }
.tabs .tab { padding: 14px 20px; font-size: 14px; font-weight: 500; color: #64748b; background: none; border: none; border-bottom: 3px solid transparent; margin-bottom: -2px; cursor: pointer; font-family: inherit; }
.tabs .tab:hover { color: #0ea5e9; }
.tabs .tab.active { color: #0ea5e9; border-bottom-color: #0ea5e9; }
.tab-content { display: none; padding: 24px; background: var(--card-bg); border-radius: 0 0 12px 12px; box-shadow: var(--card-shadow); border: 1px solid #e2e8f0; border-top: none; min-height: 200px; }
.tab-content.active { display: block; }
.tab-panel { color: #64748b; font-size: 14px; }
.tab-panel.empty { font-style: italic; }
@media (max-width: 640px) { .detail-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="container" style="max-width: 1000px;">
    <a href="{{ route('customers.index') }}" class="back-link">← Back to Customers</a>

    <div class="page-header">
        <h1>{{ trim(($customer->name ?? '') . ' ' . ($customer->surname ?? '')) ?: 'Customer #' . $customer->id }}</h1>
    </div>

    <div class="card">
        <div class="detail-grid">
            <div class="detail-item">
                <label>Name</label>
                <div class="value {{ $customer->name ? '' : 'empty' }}">{{ $customer->name ?? '—' }}</div>
            </div>
            <div class="detail-item">
                <label>Surname</label>
                <div class="value {{ $customer->surname ? '' : 'empty' }}">{{ $customer->surname ?? '—' }}</div>
            </div>
            <div class="detail-item">
                <label>Company name</label>
                <div class="value {{ $customer->company_name ? '' : 'empty' }}">{{ $customer->company_name ?? '—' }}</div>
            </div>
            <div class="detail-item">
                <label>Email</label>
                <div class="value {{ $customer->email ? '' : 'empty' }}">{{ $customer->email ?? '—' }}</div>
            </div>
            <div class="detail-item">
                <label>Cell</label>
                <div class="value {{ $customer->cell ? '' : 'empty' }}">{{ $customer->cell ?? '—' }}</div>
            </div>
            <div class="detail-item">
                <label>Tel</label>
                <div class="value {{ $customer->tel ? '' : 'empty' }}">{{ $customer->tel ?? '—' }}</div>
            </div>
            <div class="detail-item">
                <label>ID Nr</label>
                <div class="value {{ $customer->ID_nr ? '' : 'empty' }}">{{ $customer->ID_nr ?? '—' }}</div>
            </div>
            <div class="detail-item">
                <label>Date of birth</label>
                <div class="value {{ $customer->date_of_birth ? '' : 'empty' }}">{{ $customer->date_of_birth ? $customer->date_of_birth->format('d M Y') : '—' }}</div>
            </div>
            <div class="detail-item">
                <label>Handset preference</label>
                <div class="value {{ $customer->handset_preference ? '' : 'empty' }}">{{ $customer->handset_preference ?? '—' }}</div>
            </div>
            <div class="detail-item">
                <label>Source</label>
                <div class="value {{ $customer->source ? '' : 'empty' }}">{{ $customer->source ?? '—' }}</div>
            </div>
        </div>
    </div>

    <div class="card" style="padding: 0; margin-bottom: 0;">
        <div class="tabs">
            <button type="button" class="tab active" data-tab="files">Files</button>
            <button type="button" class="tab" data-tab="notes">Notes</button>
            <button type="button" class="tab" data-tab="repairs">Repairs</button>
        </div>
        <div id="files" class="tab-content active">
            <div class="tab-panel empty">No files yet. File management can be added here.</div>
        </div>
        <div id="notes" class="tab-content">
            <div class="tab-panel empty">No notes yet. Notes can be added here.</div>
        </div>
        <div id="repairs" class="tab-content">
            @if($customer->repairs->count() > 0)
                <div style="overflow-x: auto;">
                    <table class="table" style="margin: 0;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Status</th>
                                <th>Phone / IMEI</th>
                                <th>Date Booked</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->repairs as $repair)
                                <tr>
                                    <td>{{ $repair->id }}</td>
                                    <td><span class="status-badge status-{{ $repair->ticket_status }}">{{ $repair->status_label }}</span></td>
                                    <td>{{ $repair->phone ?? '—' }} / {{ $repair->imei ?? '—' }}</td>
                                    <td>{{ $repair->created_at->format('M j, Y H:i') }}</td>
                                    <td><a href="{{ route('repairs.show', $repair) }}" class="btn btn-primary btn-sm" style="padding: 6px 12px; font-size: 13px;">View</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p style="margin-top: 16px; font-size: 14px; color: #64748b;">{{ $customer->repairs->count() }} repair(s) linked to this customer.</p>
            @else
                <div class="tab-panel empty">No repairs linked to this customer yet.</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.tab').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var tabId = this.getAttribute('data-tab');
        document.querySelectorAll('.tab').forEach(function(t) { t.classList.remove('active'); });
        document.querySelectorAll('.tab-content').forEach(function(c) { c.classList.remove('active'); });
        this.classList.add('active');
        document.getElementById(tabId).classList.add('active');
    });
});
</script>
@endpush
