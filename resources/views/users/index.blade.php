@extends('layouts.app')

@section('title', 'Manage System Users - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Manage System Users</h1>
        @can('create', \App\Models\User::class)
            <a href="{{ route('users.create') }}" class="btn btn-primary">Add System User</a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card" style="overflow-x: auto;">
        @if($users->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->roles->count() > 0)
                                    @foreach($user->roles as $role)
                                        <span class="badge" style="background: #e0f2fe; color: #0369a1; font-size: 12px; padding: 4px 8px; border-radius: 6px; margin-right: 4px;">{{ ucwords(str_replace('-', ' ', $role->name)) }}</span>
                                    @endforeach
                                @else
                                    <span style="color: #94a3b8;">—</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('M j, Y') }}</td>
                            <td>
                                <div class="actions">
                                    @can('update', $user)
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;">Edit</a>
                                    @endcan
                                    @can('delete', $user)
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this user? They will no longer be able to log in.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" style="padding: 8px 16px; font-size: 14px;">Delete</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $users->links() }}</div>
        @else
            <div class="empty-state">
                <p>No system users found.</p>
                @can('create', \App\Models\User::class)
                    <a href="{{ route('users.create') }}" class="btn btn-primary">Add System User</a>
                @endcan
            </div>
        @endif
    </div>
</div>
@endsection
