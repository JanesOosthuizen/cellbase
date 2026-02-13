@extends('layouts.app')

@section('title', 'Import Devices from Excel - ' . config('app.name'))

@push('styles')
<style>
.warning-box { padding: 16px; background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; margin-bottom: 24px; font-size: 14px; color: #92400e; }
.info-box { padding: 20px; background: #f8fafc; border-radius: 8px; margin-bottom: 24px; }
.info-box h3 { font-size: 15px; font-weight: 600; color: #334155; margin-bottom: 12px; }
.info-box ul { margin: 0; padding-left: 20px; color: #64748b; font-size: 14px; line-height: 1.7; }
</style>
@endpush

@section('content')
<div class="container" style="max-width: 720px;">
    <div class="page-header" style="flex-direction: column; align-items: flex-start;">
        <h1>Import Devices from Excel</h1>
        <p style="color: #64748b; font-size: 15px; margin-top: 4px;">Upload an Excel file to import devices in bulk</p>
    </div>

    @if(session('error'))
        <div class="alert-danger">{{ session('error') }}</div>
    @endif

    <div class="warning-box">
        <p><strong>Warning:</strong> Importing a new Excel file will archive all current devices and replace them with the new data. This action cannot be undone.</p>
    </div>

    <div class="card">
        <div class="info-box">
            <h3>Excel File Format</h3>
            <ul>
                <li>The first row should contain headers (will be skipped)</li>
                <li>Required columns: Product Code, Model</li>
                <li>Optional columns: Bar Code, Make, Cost Excl, Cost Incl, RSP Excl, RSP Incl</li>
                <li>Column order: Product Code | Bar Code | Make | Model | Cost Excl | Cost Incl | RSP Excl | RSP Incl</li>
                <li>If "Make" doesn't exist in the database, it will be created automatically</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('devices.import.excel') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="excel_file">Excel File (.xlsx, .xls, or .csv)</label>
                <input type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                @error('excel_file')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-actions">
                <a href="{{ route('devices.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure? This will archive all current devices and replace them with the new data.');">Import Devices</button>
            </div>
        </form>
    </div>
</div>
@endsection
