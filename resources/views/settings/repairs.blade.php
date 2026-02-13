@extends('layouts.app')

@section('title', 'Repairs Settings - ' . config('app.name'))

@push('head')
<script src="https://cdn.tiny.cloud/1/4d8267me3qo92m3plnbmn7yvy9io46kcbcij52kyf0arqs1z/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
@endpush

@push('styles')
<style>
.form-group textarea { min-height: 120px; }
</style>
@endpush

@section('content')
<div class="container" style="max-width: 900px;">
    <div class="page-header">
        <h1>Repairs Settings</h1>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('settings.repairs.update') }}" id="repairSettingsForm">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="repair_form_terms">Repair Form Terms &amp; Conditions</label>
                <textarea id="repair_form_terms" name="repair_form_terms" class="repairs-editor" rows="12">{{ old('repair_form_terms', $settings->repair_form_terms) }}</textarea>
            </div>
            <div class="form-group">
                <label for="repair_invoice_terms">Repair Invoice Terms &amp; Conditions</label>
                <textarea id="repair_invoice_terms" name="repair_invoice_terms" class="repairs-editor" rows="12">{{ old('repair_invoice_terms', $settings->repair_invoice_terms) }}</textarea>
            </div>
            <div class="form-actions">
                <a href="{{ route('settings') }}" class="btn btn-secondary">Back to Settings</a>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
        selector: 'textarea.repairs-editor',
        height: 280,
        menubar: false,
        plugins: 'lists link code table',
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link | code',
        content_style: 'body { font-family: DM Sans, sans-serif; font-size: 14px; }',
        promotion: false,
        branding: false
    });
    document.getElementById('repairSettingsForm').addEventListener('submit', function() {
        tinymce.triggerSave();
    });
});
</script>
@endpush
