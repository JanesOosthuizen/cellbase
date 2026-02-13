@extends('layouts.app')

@section('title', 'Edit Loan Device - ' . config('app.name'))

@push('styles')
<style>
.device-select-wrap { position: relative; }
.device-select-input { width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-family: inherit; background: #fff; }
.device-select-input:focus { outline: none; border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,0.15); }
.device-select-input::placeholder { color: #94a3b8; }
.device-select-dropdown { position: absolute; top: 100%; left: 0; right: 0; margin-top: 4px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-height: 240px; overflow-y: auto; z-index: 20; display: none; }
.device-select-dropdown.active { display: block; }
.device-select-option { padding: 10px 12px; cursor: pointer; font-size: 14px; color: #334155; border-bottom: 1px solid #f1f5f9; }
.device-select-option:hover { background: #f8fafc; }
.device-select-option:last-child { border-bottom: none; }
.device-select-option.no-results { color: #94a3b8; cursor: default; }
.device-select-option.no-results:hover { background: #fff; }
</style>
@endpush

@section('content')
<div class="container" style="max-width: 560px;">
    <div class="page-header">
        <h1>Edit Loan Device</h1>
        <a href="{{ route('loan-devices.index') }}" class="btn btn-secondary">Back to loan devices</a>
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
        <form method="POST" action="{{ route('loan-devices.update', $loanDevice) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="device_search">Phone (Device) <span style="color: #dc2626;">*</span></label>
                <div class="device-select-wrap">
                    <input type="text" id="device_search" class="device-select-input" autocomplete="off" placeholder="Type to search by product code, make or model..."
                        value="">
                    <input type="hidden" name="device_id" id="device_id" value="{{ old('device_id', $loanDevice->device_id) }}" required>
                    <div id="device_dropdown" class="device-select-dropdown" role="listbox"></div>
                </div>
                @error('device_id')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="imei">IMEI</label>
                <input type="text" id="imei" name="imei" value="{{ old('imei', $loanDevice->imei) }}" maxlength="50" placeholder="e.g. 15 digits">
                @error('imei')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-actions">
                <a href="{{ route('loan-devices.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var devices = @json($devices->map(function($d) {
        $label = trim(($d->product_code ?? '') . ' ' . ($d->manufacturer ? $d->manufacturer->name . ' ' : '') . ($d->model ?? ''));
        return ['id' => $d->id, 'label' => $label];
    })->values());
    var selectedId = {{ json_encode(old('device_id', $loanDevice->device_id)) }};
    var wrap = document.querySelector('.device-select-wrap');
    var input = document.getElementById('device_search');
    var hidden = document.getElementById('device_id');
    var dropdown = document.getElementById('device_dropdown');

    function renderList(filter) {
        var term = (filter || '').toLowerCase().trim();
        var list = term
            ? devices.filter(function(d) { return d.label.toLowerCase().indexOf(term) !== -1; })
            : devices;
        dropdown.innerHTML = '';
        if (list.length === 0) {
            var el = document.createElement('div');
            el.className = 'device-select-option no-results';
            el.textContent = 'No matching devices';
            dropdown.appendChild(el);
        } else {
            list.forEach(function(d) {
                var el = document.createElement('div');
                el.className = 'device-select-option';
                el.textContent = d.label;
                el.setAttribute('data-id', d.id);
                el.setAttribute('role', 'option');
                el.addEventListener('click', function() {
                    hidden.value = d.id;
                    input.value = d.label;
                    dropdown.classList.remove('active');
                    input.blur();
                });
                dropdown.appendChild(el);
            });
        }
        dropdown.classList.add('active');
    }

    input.addEventListener('focus', function() {
        renderList(input.value);
    });
    input.addEventListener('input', function() {
        renderList(input.value);
    });
    input.addEventListener('blur', function() {
        setTimeout(function() {
            dropdown.classList.remove('active');
        }, 200);
    });
    document.addEventListener('click', function(e) {
        if (!wrap.contains(e.target)) dropdown.classList.remove('active');
    });

    if (selectedId) {
        var chosen = devices.find(function(d) { return String(d.id) === String(selectedId); });
        if (chosen) input.value = chosen.label;
    }
})();
</script>
@endpush
@endsection
