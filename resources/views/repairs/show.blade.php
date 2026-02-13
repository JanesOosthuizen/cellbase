@extends('layouts.app')

@section('title', 'Repair #' . $repair->id . ' - ' . config('app.name'))

@push('styles')
<style>
.page-header-actions { display: flex; gap: 12px; align-items: center; }
.cards-row { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start; }
@media (max-width: 900px) { .cards-row { grid-template-columns: 1fr; } }
.right-column { display: flex; flex-direction: column; gap: 24px; }
.fault-status-card .fault-text { font-size: 14px; color: #334155; line-height: 1.5; white-space: pre-wrap; margin-bottom: 16px; }
.fault-status-card .status-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.fault-status-card .status-label { font-weight: 500; color: #64748b; font-size: 14px; }
.detail-grid { display: grid; grid-template-columns: 160px 1fr; gap: 12px 24px; align-items: start; }
.detail-label { font-weight: 500; color: #64748b; font-size: 14px; }
.detail-value { color: #334155; font-size: 14px; }
.detail-value a { color: #0ea5e9; text-decoration: none; }
.detail-value a:hover { text-decoration: underline; }
.history-list { list-style: none; margin: 0; padding: 0; }
.history-item { padding: 14px 0; border-bottom: 1px solid #e2e8f0; display: flex; gap: 16px; align-items: flex-start; }
.history-item:last-child { border-bottom: none; }
.history-time { font-size: 13px; color: #64748b; white-space: nowrap; }
.history-content { flex: 1; }
.history-type { font-weight: 600; color: #334155; font-size: 14px; margin-bottom: 2px; }
.history-desc { font-size: 14px; color: #64748b; }
.history-by { font-size: 12px; color: #94a3b8; margin-top: 4px; }
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.6); z-index: 1000; align-items: center; justify-content: center; }
.modal.active { display: flex; }
.modal-content { background: #fff; border-radius: 12px; padding: 28px; max-width: 400px; width: 90%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
.modal-header { margin-bottom: 20px; }
.modal-header h2 { font-size: 18px; font-weight: 600; color: #0f172a; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Repair #{{ $repair->id }}</h1>
        <div class="page-header-actions">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('statusModal').classList.add('active')">Update status</button>
            <a href="{{ route('repairs.edit', $repair) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="cards-row">
        <div class="card">
            <h2>Details</h2>
            <div class="detail-grid">
                <span class="detail-label">Customer</span>
                <span class="detail-value">
                    @if($repair->customer)
                        <a href="{{ route('customers.show', $repair->customer) }}">{{ $repair->customer->name }} {{ $repair->customer->surname }}</a>
                    @else
                        —
                    @endif
                </span>
                <span class="detail-label">Phone</span>
                <span class="detail-value">{{ $repair->phone ?? '—' }}</span>
                <span class="detail-label">IMEI</span>
                <span class="detail-value">{{ $repair->imei ?? '—' }}</span>
                <span class="detail-label">Cell Nr</span>
                <span class="detail-value">{{ $repair->cell_nr ?? '—' }}</span>
                <span class="detail-label">Contact Nr</span>
                <span class="detail-value">{{ $repair->contact_nr ?? '—' }}</span>
                <span class="detail-label">Allocated To</span>
                <span class="detail-value">
                    @if($repair->allocatedTo)
                        {{ $repair->allocatedTo->company ?: $repair->allocatedTo->name }} {{ $repair->allocatedTo->surname }}
                    @else
                        —
                    @endif
                </span>
                <span class="detail-label">Ticket Status</span>
                <span class="detail-value"><span class="status-badge status-{{ $repair->ticket_status }}">{{ $repair->status_label }}</span></span>
                <span class="detail-label">Date Booked</span>
                <span class="detail-value">{{ $repair->created_at->format('M j, Y H:i') }}</span>
                <span class="detail-label">Fault Description</span>
                <span class="detail-value">{{ $repair->fault_description ? nl2br(e($repair->fault_description)) : '—' }}</span>
            </div>
        </div>

        <div class="right-column">
            <div class="card fault-status-card">
                <h2>Fault &amp; status</h2>
                <div class="fault-text">{!! $repair->fault_description ? nl2br(e($repair->fault_description)) : 'No fault description.' !!}</div>
                <div class="status-row">
                    <span class="status-label">Current status:</span>
                    <span class="status-badge status-{{ $repair->ticket_status }}">{{ $repair->status_label }}</span>
                </div>
            </div>

            <div class="card">
                <h2>History</h2>
                @if($repair->events->count() > 0)
                    <ul class="history-list">
                        @foreach($repair->events as $event)
                            <li class="history-item">
                                <span class="history-time">{{ $event->created_at->format('M j, Y H:i') }}</span>
                                <div class="history-content">
                                    <div class="history-type">{{ ucfirst(str_replace('_', ' ', $event->event_type)) }}</div>
                                    <div class="history-desc">{{ $event->description }}</div>
                                    <div class="history-by">Actioned by {{ $event->user?->name ?? 'System' }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p style="color: #64748b; font-size: 14px;">No events recorded yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div id="statusModal" class="modal" onclick="if (event.target === this) this.classList.remove('active')">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Update status</h2>
        </div>
        <form method="POST" action="{{ route('repairs.update-status', $repair) }}">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label for="modal_ticket_status">New status</label>
                <select id="modal_ticket_status" name="ticket_status" required>
                    @foreach(\App\Models\Repair::STATUS_LABELS as $value => $label)
                        <option value="{{ $value }}" {{ $repair->ticket_status === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div id="sentAwayGroup" class="form-group" style="display: none;">
                <label for="modal_allocated_to">Sent to (supplier)</label>
                <select id="modal_allocated_to" name="allocated_to">
                    <option value="">— Select supplier —</option>
                    @foreach($externalUsers as $eu)
                        <option value="{{ $eu->id }}" {{ $repair->allocated_to == $eu->id ? 'selected' : '' }}>
                            {{ $eu->company ?: $eu->name }} {{ $eu->surname ?: '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('statusModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update status</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var statusSelect = document.getElementById('modal_ticket_status');
    var sentAwayGroup = document.getElementById('sentAwayGroup');
    function toggleSentAway() {
        sentAwayGroup.style.display = statusSelect && statusSelect.value === 'sent_away' ? 'block' : 'none';
    }
    if (statusSelect) statusSelect.addEventListener('change', toggleSentAway);
    var modal = document.getElementById('statusModal');
    if (modal) modal.addEventListener('click', function() {
        if (this.classList.contains('active')) toggleSentAway();
    });
    toggleSentAway();
})();
</script>
@endpush
