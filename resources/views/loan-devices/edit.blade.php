@extends('layouts.app')

@section('title', 'Edit Loan Device - ' . config('app.name'))

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
                <label for="device_id">Phone (Device) <span style="color: #dc2626;">*</span></label>
                <select id="device_id" name="device_id" required>
                    <option value="">— Select phone —</option>
                    @foreach($devices as $d)
                        <option value="{{ $d->id }}" {{ old('device_id', $loanDevice->device_id) == $d->id ? 'selected' : '' }}>
                            {{ $d->product_code }} {{ $d->manufacturer ? $d->manufacturer->name . ' ' : '' }}{{ $d->model }}
                        </option>
                    @endforeach
                </select>
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
@endsection
