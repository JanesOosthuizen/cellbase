@extends('layouts.app')

@section('title', 'Edit Repair #' . $repair->id . ' - ' . config('app.name'))

@push('styles')
<style>
.form-group textarea { min-height: 100px; resize: vertical; }
.loan-device-wrap { position: relative; }
.loan-device-input { width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-family: inherit; background: #fff; }
.loan-device-input:focus { outline: none; border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,0.15); }
.loan-device-dropdown { position: absolute; top: 100%; left: 0; right: 0; margin-top: 4px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-height: 220px; overflow-y: auto; z-index: 20; display: none; }
.loan-device-dropdown.active { display: block; }
.loan-device-option { padding: 10px 12px; cursor: pointer; font-size: 14px; color: #334155; border-bottom: 1px solid #f1f5f9; }
.loan-device-option:hover { background: #f8fafc; }
.loan-device-option.no-results { color: #94a3b8; cursor: default; }
</style>
@endpush

@section('content')
<div class="container" style="max-width: 720px;">
    <div class="page-header">
        <h1>Edit Repair #{{ $repair->id }}</h1>
    </div>

    @if($errors->any())
        <div class="alert-danger">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('repairs.update', $repair) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="customer_id">Customer (link)</label>
                <select id="customer_id" name="customer_id">
                    <option value="">— Select customer —</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id', $repair->customer_id) == $c->id ? 'selected' : '' }}>
                            {{ $c->surname }}, {{ $c->name }}{{ $c->company_name ? ' (' . $c->company_name . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $repair->phone) }}">
            </div>
            <div class="form-group">
                <label for="imei">IMEI</label>
                <input type="text" id="imei" name="imei" value="{{ old('imei', $repair->imei) }}" maxlength="20">
            </div>
            <div class="form-group">
                <label for="cell_nr">Cell Nr</label>
                <input type="text" id="cell_nr" name="cell_nr" value="{{ old('cell_nr', $repair->cell_nr) }}">
            </div>
            <div class="form-group">
                <label for="contact_nr">Contact Nr</label>
                <input type="text" id="contact_nr" name="contact_nr" value="{{ old('contact_nr', $repair->contact_nr) }}">
            </div>
            <div class="form-group">
                <label for="allocated_to">Allocated To (supplier)</label>
                <select id="allocated_to" name="allocated_to">
                    <option value="">— Select supplier —</option>
                    @foreach($externalUsers as $eu)
                        <option value="{{ $eu->id }}" {{ old('allocated_to', $repair->allocated_to) == $eu->id ? 'selected' : '' }}>
                            {{ $eu->company ?: $eu->name }} {{ $eu->surname ?: '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="loan_device_search">Loan Device (allocated to customer)</label>
                <div class="loan-device-wrap">
                    <input type="text" id="loan_device_search" class="loan-device-input" autocomplete="off" placeholder="Type to search by device or IMEI...">
                    <input type="hidden" name="loan_device_id" id="loan_device_id" value="{{ old('loan_device_id', $repair->loan_device_id) }}">
                    <div id="loan_device_dropdown" class="loan-device-dropdown" role="listbox"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="fault_description">Fault Description</label>
                <textarea id="fault_description" name="fault_description">{{ old('fault_description', $repair->fault_description) }}</textarea>
            </div>
            <div class="form-group">
                <label for="ticket_status">Ticket Status</label>
                <select id="ticket_status" name="ticket_status">
                    @foreach(\App\Models\Repair::STATUS_LABELS as $value => $label)
                        <option value="{{ $value }}" {{ old('ticket_status', $repair->ticket_status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-actions">
                <a href="{{ route('repairs.show', $repair) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Repair</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var loanDevices = @json($loanDevices->map(function($ld) {
        $label = trim(($ld->device->product_code ?? '') . ' ' . ($ld->device->manufacturer ? $ld->device->manufacturer->name . ' ' : '') . ($ld->device->model ?? '') . ($ld->imei ? ' (IMEI: ' . $ld->imei . ')' : ''));
        return ['id' => $ld->id, 'label' => $label];
    })->values());
    var selectedId = {{ json_encode(old('loan_device_id', $repair->loan_device_id)) }};
    var wrap = document.querySelector('.loan-device-wrap');
    var input = document.getElementById('loan_device_search');
    var hidden = document.getElementById('loan_device_id');
    var dropdown = document.getElementById('loan_device_dropdown');

    function renderList(filter) {
        var term = (filter || '').toLowerCase().trim();
        var list = term ? loanDevices.filter(function(d) { return d.label.toLowerCase().indexOf(term) !== -1; }) : loanDevices;
        dropdown.innerHTML = '';
        var opt = document.createElement('div');
        opt.className = 'loan-device-option';
        opt.textContent = '— None —';
        opt.setAttribute('data-id', '');
        opt.addEventListener('click', function() { hidden.value = ''; input.value = ''; dropdown.classList.remove('active'); });
        dropdown.appendChild(opt);
        if (list.length === 0 && term) {
            var nr = document.createElement('div');
            nr.className = 'loan-device-option no-results';
            nr.textContent = 'No matching loan devices';
            dropdown.appendChild(nr);
        } else {
            list.forEach(function(d) {
                var el = document.createElement('div');
                el.className = 'loan-device-option';
                el.textContent = d.label;
                el.setAttribute('data-id', d.id);
                el.addEventListener('click', function() { hidden.value = d.id; input.value = d.label; dropdown.classList.remove('active'); });
                dropdown.appendChild(el);
            });
        }
        dropdown.classList.add('active');
    }
    input.addEventListener('focus', function() { renderList(input.value); });
    input.addEventListener('input', function() { renderList(input.value); });
    input.addEventListener('blur', function() { setTimeout(function() { dropdown.classList.remove('active'); }, 200); });
    document.addEventListener('click', function(e) { if (wrap && !wrap.contains(e.target)) dropdown.classList.remove('active'); });
    if (selectedId) {
        var chosen = loanDevices.find(function(d) { return String(d.id) === String(selectedId); });
        if (chosen) input.value = chosen.label;
    }
})();
</script>
@endpush
@endsection
