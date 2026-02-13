@extends('layouts.app')

@section('title', 'Add Device - ' . config('app.name'))

@section('content')
<div class="container" style="max-width: 720px;">
    <div class="page-header">
        <h1>Add Device</h1>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('devices.store') }}">
            @csrf
            <div class="form-group">
                <label for="product_code">Product Code <span style="color: #dc2626;">*</span></label>
                <input type="text" id="product_code" name="product_code" value="{{ old('product_code') }}" required autofocus>
                @error('product_code')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="bar_code">Bar Code</label>
                <input type="text" id="bar_code" name="bar_code" value="{{ old('bar_code') }}">
                @error('bar_code')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="manufacturer_id">Manufacturer <span style="color: #dc2626;">*</span></label>
                <select id="manufacturer_id" name="manufacturer_id" required>
                    <option value="">Select a manufacturer</option>
                    @foreach($manufacturers as $manufacturer)
                        <option value="{{ $manufacturer->id }}" {{ old('manufacturer_id') == $manufacturer->id ? 'selected' : '' }}>{{ $manufacturer->name }}</option>
                    @endforeach
                </select>
                @error('manufacturer_id')<div class="error">{{ $message }}</div>@enderror
                @if($manufacturers->isEmpty())
                    <div style="font-size: 13px; color: #64748b; margin-top: 6px;">No manufacturers found. <a href="{{ route('manufacturers.index') }}" style="color: #0ea5e9;">Add a manufacturer first</a>.</div>
                @endif
            </div>
            <div class="form-group">
                <label for="model">Model <span style="color: #dc2626;">*</span></label>
                <input type="text" id="model" name="model" value="{{ old('model') }}" required>
                @error('model')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="cost_excl">Cost Excl</label>
                    <input type="number" id="cost_excl" name="cost_excl" value="{{ old('cost_excl') }}" step="0.01" min="0">
                    @error('cost_excl')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="cost_incl">Cost Incl</label>
                    <input type="number" id="cost_incl" name="cost_incl" value="{{ old('cost_incl') }}" step="0.01" min="0">
                    @error('cost_incl')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="rsp_excl">RSP Excl</label>
                    <input type="number" id="rsp_excl" name="rsp_excl" value="{{ old('rsp_excl') }}" step="0.01" min="0">
                    @error('rsp_excl')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="rsp_incl">RSP Incl</label>
                    <input type="number" id="rsp_incl" name="rsp_incl" value="{{ old('rsp_incl') }}" step="0.01" min="0">
                    @error('rsp_incl')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('devices.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Device</button>
            </div>
        </form>
    </div>
</div>
@endsection
