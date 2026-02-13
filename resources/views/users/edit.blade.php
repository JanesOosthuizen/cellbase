@extends('layouts.app')

@section('title', 'Edit System User - ' . config('app.name'))

@section('content')
<div class="container" style="max-width: 560px;">
    <div class="page-header">
        <h1>Edit System User</h1>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Back to users</a>
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
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Name <span style="color: #dc2626;">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="255">
                @error('name')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="email">Email <span style="color: #dc2626;">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255">
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="password">New password</label>
                <input type="password" id="password" name="password" autocomplete="new-password">
                @error('password')<div class="error">{{ $message }}</div>@enderror
                <p style="font-size: 12px; color: #64748b; margin-top: 4px;">Leave blank to keep current password. Minimum 8 characters if set.</p>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm new password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
            </div>
            <div class="form-group">
                <label>Roles</label>
                <div style="display: flex; flex-wrap: wrap; gap: 12px 24px; margin-top: 8px;">
                    @foreach($roles as $role)
                        <label style="display: flex; align-items: center; gap: 8px; font-weight: normal; cursor: pointer;">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ (isset($user->roles) && $user->roles->contains('id', $role->id)) || in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                            {{ ucwords(str_replace('-', ' ', $role->name)) }}
                        </label>
                    @endforeach
                </div>
                @error('roles')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-actions">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
