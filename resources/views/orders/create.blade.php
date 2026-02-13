@extends('layouts.app')

@section('title', 'Add Order - ' . config('app.name'))

@push('styles')
<style>
.form-group textarea { min-height: 100px; resize: vertical; }
.device-search-wrap { position: relative; }
.device-search-results { position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 8px 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-height: 220px; overflow-y: auto; z-index: 10; display: none; }
.device-search-results.active { display: block; }
.device-search-result { padding: 12px 14px; cursor: pointer; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
.device-search-result:hover { background: #f8fafc; }
.device-search-result:last-child { border-bottom: none; }
.device-selected { margin-top: 8px; padding: 10px 12px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 14px; color: #166534; display: none; }
.device-selected.active { display: block; }
.device-selected .clear-device { margin-left: 8px; color: #0ea5e9; cursor: pointer; font-weight: 500; }
</style>
@endpush

@section('content')
<div class="container" style="max-width: 720px;">
    <div class="page-header">
        <h1>Add Order</h1>
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
        <form method="POST" action="{{ route('orders.store') }}">
            @csrf
            <div class="form-group">
                <label for="customer_id">Customer</label>
                <select id="customer_id" name="customer_id">
                    <option value="">— Select customer —</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->surname }}, {{ $c->name }}{{ $c->company_name ? ' (' . $c->company_name . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="phone_search">Phone (search from devices)</label>
                <div class="device-search-wrap">
                    <input type="text" id="phone_search" autocomplete="off" placeholder="Type product code, model or manufacturer..."
                        value="{{ old('phone_search') }}">
                    <input type="hidden" name="device_id" id="device_id" value="{{ old('device_id') }}">
                    <div id="device_search_results" class="device-search-results"></div>
                </div>
                <div id="device_selected" class="device-selected {{ $selectedDevice ? 'active' : '' }}">
                    <span id="device_selected_label">{{ $selectedDevice ? $selectedDevice->product_code . ' - ' . ($selectedDevice->manufacturer ? $selectedDevice->manufacturer->name : '') . ' ' . $selectedDevice->model : '' }}</span>
                    <a href="#" class="clear-device" id="clear_device">Clear</a>
                </div>
            </div>
            <div class="form-group">
                <label for="cell_nr">Cell Nr</label>
                <input type="text" id="cell_nr" name="cell_nr" value="{{ old('cell_nr') }}">
            </div>
            <div class="form-group">
                <label for="note">Note</label>
                <textarea id="note" name="note" placeholder="Optional note...">{{ old('note') }}</textarea>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    @foreach(\App\Models\Order::STATUS_LABELS as $value => $label)
                        <option value="{{ $value }}" {{ old('status', 'to_be_ordered') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-actions">
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Order</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var searchInput = document.getElementById('phone_search');
    var deviceIdInput = document.getElementById('device_id');
    var resultsDiv = document.getElementById('device_search_results');
    var selectedDiv = document.getElementById('device_selected');
    var selectedLabel = document.getElementById('device_selected_label');
    var clearBtn = document.getElementById('clear_device');
    var searchTimeout;

    function showResults(devices) {
        resultsDiv.innerHTML = '';
        if (!devices || devices.length === 0) {
            resultsDiv.classList.remove('active');
            return;
        }
        devices.forEach(function(d) {
            var el = document.createElement('div');
            el.className = 'device-search-result';
            el.textContent = d.label;
            el.dataset.id = d.id;
            el.dataset.label = d.label;
            el.addEventListener('click', function() {
                deviceIdInput.value = d.id;
                selectedLabel.textContent = d.label;
                selectedDiv.classList.add('active');
                searchInput.value = '';
                resultsDiv.classList.remove('active');
                resultsDiv.innerHTML = '';
            });
            resultsDiv.appendChild(el);
        });
        resultsDiv.classList.add('active');
    }

    searchInput.addEventListener('input', function() {
        var q = this.value.trim();
        clearTimeout(searchTimeout);
        if (q.length < 2) {
            resultsDiv.classList.remove('active');
            resultsDiv.innerHTML = '';
            return;
        }
        searchTimeout = setTimeout(function() {
            fetch('{{ route("api.orders.search-devices") }}?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            }).then(function(r) { return r.json(); }).then(function(data) {
                showResults(data.devices || []);
            }).catch(function() { showResults([]); });
        }, 250);
    });

    searchInput.addEventListener('blur', function() {
        setTimeout(function() {
            resultsDiv.classList.remove('active');
        }, 200);
    });

    clearBtn.addEventListener('click', function(e) {
        e.preventDefault();
        deviceIdInput.value = '';
        selectedDiv.classList.remove('active');
        selectedLabel.textContent = '';
    });

    if (deviceIdInput.value && selectedLabel.textContent) {
        selectedDiv.classList.add('active');
    }
})();
</script>
@endpush
