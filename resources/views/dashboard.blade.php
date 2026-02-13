@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="card" style="padding: 28px;">
        <h1 style="font-size: 26px; font-weight: 700; color: #0f172a; margin-bottom: 8px;">Welcome, {{ Auth::user()->name }}</h1>
        <p style="color: #64748b; font-size: 15px;">You're logged in to your dashboard</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
        <div class="card">
            <h3 style="font-size: 15px; font-weight: 600; color: #334155; margin-bottom: 12px;">Your Account</h3>
            <p style="font-size: 14px; color: #64748b;"><strong>Email:</strong> {{ Auth::user()->email }}</p>
            <p style="font-size: 14px; color: #64748b; margin-top: 8px;"><strong>Member since:</strong> {{ Auth::user()->created_at->format('F j, Y') }}</p>
        </div>

        <div class="card">
            <h3 style="font-size: 15px; font-weight: 600; color: #334155; margin-bottom: 12px;">Your Roles</h3>
            @if(Auth::user()->roles->count() > 0)
                @foreach(Auth::user()->roles as $role)
                    <span style="display: inline-block; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; margin-right: 6px; margin-bottom: 6px; background: #e0f2fe; color: #0369a1;">
                        {{ ucwords(str_replace('-', ' ', $role->name)) }}
                    </span>
                @endforeach
            @else
                <p style="font-size: 14px; color: #64748b;">No roles assigned</p>
            @endif
        </div>

        <div class="card">
            <h3 style="font-size: 15px; font-weight: 600; color: #334155; margin-bottom: 12px;">Your Permissions</h3>
            @php
                $permissions = Auth::user()->roles->flatMap->permissions->unique('id');
            @endphp
            @if($permissions->count() > 0)
                @foreach($permissions as $permission)
                    <span style="display: inline-block; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; margin-right: 6px; margin-bottom: 6px; background: #d1fae5; color: #047857;">
                        {{ ucwords(str_replace('-', ' ', $permission->name)) }}
                    </span>
                @endforeach
            @else
                <p style="font-size: 14px; color: #64748b;">No permissions assigned</p>
            @endif
        </div>
    </div>
</div>
@endsection
