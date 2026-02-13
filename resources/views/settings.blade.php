@extends('layouts.app')

@section('title', 'Settings - ' . config('app.name'))

@push('styles')
<style>
.settings-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; }
.settings-section { background: var(--card-bg); border-radius: var(--card-radius); box-shadow: var(--card-shadow); padding: 24px; }
.settings-section h2 { font-size: 17px; font-weight: 600; color: #0f172a; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0; }
.settings-links { list-style: none; padding: 0; margin: 0; }
.settings-links li { margin-bottom: 6px; }
.settings-links a { display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; text-decoration: none; color: #475569; font-size: 14px; font-weight: 500; border-radius: 8px; transition: all 0.15s; background: #f8fafc; }
.settings-links a:hover { background: #0ea5e9; color: #fff; }
.settings-links a::after { content: '→'; font-size: 14px; opacity: 0.6; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header" style="flex-direction: column; align-items: flex-start;">
        <h1>Settings</h1>
        <p style="color: #64748b; font-size: 15px; margin-top: 4px;">Manage your system settings and configurations</p>
    </div>

    <div class="settings-grid">
        <div class="settings-section">
            <h2>Item Management</h2>
            <ul class="settings-links">
                <li><a href="#">Manage Company Users</a></li>
                <li><a href="{{ route('manufacturers.index') }}">Manage Manufacturers</a></li>
                <li><a href="{{ route('devices.index') }}">Manage Devices</a></li>
            </ul>
        </div>
        <div class="settings-section">
            <h2>External Users</h2>
            <ul class="settings-links">
                <li><a href="{{ route('external-users.index') }}">Manage External Users</a></li>
            </ul>
        </div>
        <div class="settings-section">
            <h2>Software Settings</h2>
            <ul class="settings-links">
                <li><a href="#">Company Settings</a></li>
                <li><a href="{{ route('settings.repairs.edit') }}">Repairs Settings</a></li>
            </ul>
        </div>
        <div class="settings-section">
            <h2>Custom Settings</h2>
            <ul class="settings-links">
                <li><a href="#">Custom Fields</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection
