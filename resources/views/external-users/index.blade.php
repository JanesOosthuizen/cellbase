@extends('layouts.app')

@section('title', 'Manage External Users - ' . config('app.name'))

@push('styles')
<style>
.form-group textarea { min-height: 80px; resize: vertical; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Manage External Users</h1>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('createModal').classList.add('active')">Add External User</button>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="card" style="overflow-x: auto;">
        @if($externalUsers->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Surname</th>
                        <th>Company</th>
                        <th>Contact Number</th>
                        <th>Email Address</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($externalUsers as $eu)
                        <tr data-id="{{ $eu->id }}"
                            data-name="{{ e($eu->name) }}"
                            data-surname="{{ e($eu->surname) }}"
                            data-company="{{ e($eu->company) }}"
                            data-contact-number="{{ e($eu->contact_number) }}"
                            data-email-address="{{ e($eu->email_address) }}"
                            data-address="{{ e($eu->address) }}">
                            <td>{{ $eu->name ?? '—' }}</td>
                            <td>{{ $eu->surname ?? '—' }}</td>
                            <td>{{ $eu->company ?? '—' }}</td>
                            <td>{{ $eu->contact_number ?? '—' }}</td>
                            <td>{{ $eu->email_address ?? '—' }}</td>
                            <td>{{ \Str::limit($eu->address, 40) ?? '—' }}</td>
                            <td>
                                <div class="actions">
                                    <button type="button" class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;" onclick="openEditModal(this.closest('tr'))">Edit</button>
                                    <form method="POST" action="{{ route('external-users.destroy', $eu) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this external user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="padding: 8px 16px; font-size: 14px;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $externalUsers->links() }}</div>
        @else
            <div class="empty-state">
                <p>No external users found. These are contacts (e.g. suppliers) for linking to repair tickets.</p>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('createModal').classList.add('active')">Add External User</button>
            </div>
        @endif
    </div>
</div>

<div id="createModal" class="modal" onclick="if (event.target === this) this.classList.remove('active')">
    <div class="modal-content">
        <div class="modal-header"><h2>Add External User</h2></div>
        <form method="POST" action="{{ route('external-users.store') }}">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}">
                @error('name')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="surname">Surname</label>
                <input type="text" id="surname" name="surname" value="{{ old('surname') }}">
                @error('surname')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="company">Company</label>
                <input type="text" id="company" name="company" value="{{ old('company') }}">
                @error('company')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number</label>
                <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number') }}">
                @error('contact_number')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="email_address">Email Address</label>
                <input type="email" id="email_address" name="email_address" value="{{ old('email_address') }}">
                @error('email_address')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address">{{ old('address') }}</textarea>
                @error('address')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('createModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="modal" onclick="if (event.target === this) this.classList.remove('active')">
    <div class="modal-content">
        <div class="modal-header"><h2>Edit External User</h2></div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_name">Name</label>
                <input type="text" id="edit_name" name="name">
            </div>
            <div class="form-group">
                <label for="edit_surname">Surname</label>
                <input type="text" id="edit_surname" name="surname">
            </div>
            <div class="form-group">
                <label for="edit_company">Company</label>
                <input type="text" id="edit_company" name="company">
            </div>
            <div class="form-group">
                <label for="edit_contact_number">Contact Number</label>
                <input type="text" id="edit_contact_number" name="contact_number">
            </div>
            <div class="form-group">
                <label for="edit_email_address">Email Address</label>
                <input type="email" id="edit_email_address" name="email_address">
            </div>
            <div class="form-group">
                <label for="edit_address">Address</label>
                <textarea id="edit_address" name="address"></textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditModal(row) {
    var id = row.getAttribute('data-id');
    document.getElementById('edit_name').value = row.getAttribute('data-name') || '';
    document.getElementById('edit_surname').value = row.getAttribute('data-surname') || '';
    document.getElementById('edit_company').value = row.getAttribute('data-company') || '';
    document.getElementById('edit_contact_number').value = row.getAttribute('data-contact-number') || '';
    document.getElementById('edit_email_address').value = row.getAttribute('data-email-address') || '';
    document.getElementById('edit_address').value = row.getAttribute('data-address') || '';
    document.getElementById('editForm').action = '{{ url("external-users") }}/' + id;
    document.getElementById('editModal').classList.add('active');
}
</script>
@endpush
