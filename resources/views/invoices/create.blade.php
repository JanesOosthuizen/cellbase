@extends('layouts.app')

@section('title', 'Add Invoice - ' . config('app.name'))

@push('styles')
<style>
.form-row { grid-template-columns: 1fr 1fr 1fr; }
.help-text { font-size: 12px; color: #64748b; margin-top: 4px; }
.btn-sm { padding: 8px 16px; font-size: 14px; }
.lines-section { margin-top: 24px; }
.lines-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.lines-header h2 { font-size: 18px; font-weight: 600; color: #0f172a; }
.lines-table { width: 100%; border-collapse: collapse; margin-top: 16px; }
.lines-table thead { background: #f8fafc; }
.lines-table th { text-align: left; padding: 12px 16px; font-weight: 600; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
.lines-table td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; }
.lines-table tbody tr:hover { background: #f8fafc; }
.lines-table input, .lines-table select { width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; font-family: inherit; }
.lines-table input:focus, .lines-table select:focus { outline: none; border-color: #0ea5e9; }
.lines-table input[type="checkbox"] { width: auto; cursor: pointer; }
.checkbox-cell { text-align: center; }
.action-cell { text-align: center; }
.total-match-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.total-match-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.lines-table tfoot tr { border-top: 2px solid #e2e8f0; }
.lines-table tfoot input[readonly] { cursor: default; }
@media (max-width: 1024px) { .form-row { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Add Invoice</h1>
    </div>

    @if(session('error'))
        <div class="alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
        @csrf

        <div class="card">
            <div class="form-row">
                <div class="form-group">
                    <label for="invoice_date">Invoice Date <span style="color: #dc2626;">*</span></label>
                    <input type="date" id="invoice_date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                    @error('invoice_date')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="invoice_nr">Invoice Nr <span style="color: #dc2626;">*</span></label>
                    <input type="text" id="invoice_nr" name="invoice_nr" value="{{ old('invoice_nr') }}" placeholder="X00000" pattern="[A-Z]\d{5}" maxlength="6" required>
                    @error('invoice_nr')<div class="error">{{ $message }}</div>@enderror
                    <div class="help-text">Format: One letter followed by 5 digits (e.g., A12345)</div>
                </div>
                <div class="form-group">
                    <label for="invoice_total_excl">Invoice Total (Excl) <span style="color: #dc2626;">*</span></label>
                    <input type="number" id="invoice_total_excl" name="invoice_total_excl" value="{{ old('invoice_total_excl') }}" step="0.01" min="0" required>
                    @error('invoice_total_excl')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="card">
            <div class="lines-section">
                <div class="lines-header">
                    <h2>Invoice Lines</h2>
                    <button type="button" class="btn btn-primary btn-sm" onclick="addLine()">Add Line</button>
                </div>

                <table class="lines-table">
                    <thead>
                        <tr>
                            <th style="width: 30px;">#</th>
                            <th>Phone (Device)</th>
                            <th>IMEI</th>
                            <th style="width: 100px;" class="checkbox-cell">Is IMEI?</th>
                            <th style="width: 150px;">Price</th>
                            <th style="width: 100px;" class="action-cell">Action</th>
                        </tr>
                    </thead>
                    <tbody id="linesContainer"></tbody>
                    <tfoot>
                        <tr style="background: #f8fafc; font-weight: 600;">
                            <td colspan="4" style="text-align: right; padding-right: 16px;">Total:</td>
                            <td>
                                <input type="text" id="linesTotal" readonly value="0.00" style="background: white; font-weight: 600; text-align: right;">
                            </td>
                            <td></td>
                        </tr>
                        <tr id="totalMatchRow" style="display: none;">
                            <td colspan="6" style="padding: 8px 16px;">
                                <div id="totalMatchMessage" style="padding: 8px 12px; border-radius: 6px; font-size: 14px;"></div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Invoice</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let lineCounter = 0;
const devices = @json($devices);

document.addEventListener('DOMContentLoaded', function() {
    addLine();
});

function addLine() {
    lineCounter++;
    const container = document.getElementById('linesContainer');
    const row = document.createElement('tr');
    row.id = 'line-' + lineCounter;
    row.innerHTML = '<td>' + lineCounter + '</td>' +
        '<td><select name="lines[' + lineCounter + '][device_id]" id="lines_' + lineCounter + '_device_id">' +
        '<option value="">Select a device</option>' +
        devices.map(function(device) {
            return '<option value="' + device.id + '">' + (device.product_code || '') + ' - ' + (device.manufacturer ? device.manufacturer.name : 'N/A') + ' ' + (device.model || '') + '</option>';
        }).join('') + '</select></td>' +
        '<td><input type="text" name="lines[' + lineCounter + '][imei]" id="lines_' + lineCounter + '_imei" maxlength="15" pattern="[0-9]{15}" placeholder="15 digits">' +
        '<div class="help-text">15 digits</div><div id="imei-feedback-' + lineCounter + '" style="margin-top: 4px; font-size: 12px;"></div></td>' +
        '<td class="checkbox-cell"><input type="checkbox" name="lines[' + lineCounter + '][is_imei]" id="lines_' + lineCounter + '_is_imei" value="1"></td>' +
        '<td><input type="number" name="lines[' + lineCounter + '][price]" id="lines_' + lineCounter + '_price" step="0.01" min="0" required placeholder="0.00"></td>' +
        '<td class="action-cell"><button type="button" class="btn btn-danger btn-sm" onclick="removeLine(' + lineCounter + ')">Remove</button></td>';
    container.appendChild(row);

    var imeiInput = row.querySelector('#lines_' + lineCounter + '_imei');
    var feedbackDiv = row.querySelector('#imei-feedback-' + lineCounter);
    var imeiCheckTimeout;
    imeiInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 15);
        clearTimeout(imeiCheckTimeout);
        if (this.value.length < 15) {
            feedbackDiv.textContent = '';
            feedbackDiv.style.color = '';
            return;
        }
        imeiCheckTimeout = setTimeout(function() {
            if (this.value.length === 15) {
                var self = this;
                fetch('{{ route('api.check-imei') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ imei: self.value })
                }).then(function(r) { return r.json(); }).then(function(data) {
                    if (data.exists) {
                        feedbackDiv.textContent = '\u26a0 ' + data.message;
                        feedbackDiv.style.color = '#f59e0b';
                    } else {
                        feedbackDiv.textContent = '\u2713 ' + data.message;
                        feedbackDiv.style.color = '#10b981';
                    }
                }).catch(function() { feedbackDiv.textContent = ''; });
            }
        }.bind(this), 500);
    });

    var priceInput = row.querySelector('#lines_' + lineCounter + '_price');
    priceInput.addEventListener('input', updateTotal);
    priceInput.addEventListener('change', updateTotal);
    updateTotal();
}

function removeLine(lineId) {
    var row = document.getElementById('line-' + lineId);
    if (row) { row.remove(); updateLineNumbers(); updateTotal(); }
}

function updateLineNumbers() {
    var rows = document.querySelectorAll('#linesContainer tr');
    rows.forEach(function(row, index) {
        var firstCell = row.querySelector('td:first-child');
        if (firstCell) firstCell.textContent = index + 1;
    });
}

document.getElementById('invoice_nr').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

document.getElementById('invoice_total_excl').addEventListener('input', checkTotalMatch);
document.getElementById('invoice_total_excl').addEventListener('change', checkTotalMatch);

function updateTotal() {
    var rows = document.querySelectorAll('#linesContainer tr');
    var total = 0;
    rows.forEach(function(row) {
        var priceInput = row.querySelector('input[name*="[price]"]');
        if (priceInput && priceInput.value) total += parseFloat(priceInput.value) || 0;
    });
    document.getElementById('linesTotal').value = total.toFixed(2);
    checkTotalMatch();
}

function checkTotalMatch() {
    var linesTotal = parseFloat(document.getElementById('linesTotal').value) || 0;
    var invoiceTotal = parseFloat(document.getElementById('invoice_total_excl').value) || 0;
    var matchRow = document.getElementById('totalMatchRow');
    var matchMessage = document.getElementById('totalMatchMessage');
    var difference = Math.abs(linesTotal - invoiceTotal);
    if (difference <= 0.01) {
        matchRow.style.display = 'table-row';
        matchMessage.className = 'total-match-success';
        matchMessage.textContent = '\u2713 Totals match';
    } else {
        matchRow.style.display = 'table-row';
        matchMessage.className = 'total-match-error';
        var diff = (linesTotal - invoiceTotal).toFixed(2);
        matchMessage.textContent = linesTotal > invoiceTotal
            ? '\u2717 Lines total (' + linesTotal.toFixed(2) + ') is ' + diff + ' more than invoice total'
            : '\u2717 Lines total (' + linesTotal.toFixed(2) + ') is ' + Math.abs(diff) + ' less than invoice total';
    }
}

document.getElementById('invoiceForm').addEventListener('submit', function(e) {
    var rows = document.querySelectorAll('#linesContainer tr');
    if (rows.length === 0) { e.preventDefault(); alert('Please add at least one invoice line.'); return false; }
    var hasError = false;
    rows.forEach(function(row) {
        var price = row.querySelector('input[name*="[price]"]');
        if (!price || !price.value || parseFloat(price.value) <= 0) hasError = true;
    });
    if (hasError) { e.preventDefault(); alert('Please ensure all lines have a valid price.'); return false; }
    var linesTotal = parseFloat(document.getElementById('linesTotal').value) || 0;
    var invoiceTotal = parseFloat(document.getElementById('invoice_total_excl').value) || 0;
    if (Math.abs(linesTotal - invoiceTotal) > 0.01) {
        e.preventDefault();
        alert('The lines total must match the invoice total (excluding) before you can submit.');
        return false;
    }
});
</script>
@endpush
