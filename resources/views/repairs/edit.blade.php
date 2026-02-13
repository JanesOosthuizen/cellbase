@extends('layouts.app')

@section('title', 'Edit Repair #' . $repair->id . ' - ' . config('app.name'))

@push('styles')
<style>
.form-group textarea { min-height: 100px; resize: vertical; }
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
@endsection
