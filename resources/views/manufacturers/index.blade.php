@extends('layouts.app')

@section('title', 'Manage Manufacturers - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Manage Manufacturers</h1>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('createModal').classList.add('active')">Add New Manufacturer</button>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        @if($manufacturers->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($manufacturers as $manufacturer)
                        <tr data-update-url="{{ route('manufacturers.update', $manufacturer) }}" data-name="{{ e($manufacturer->name) }}">
                            <td>{{ $manufacturer->name }}</td>
                            <td>{{ $manufacturer->created_at->format('M j, Y') }}</td>
                            <td>
                                <div class="actions">
                                    <button type="button" class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;" onclick="openEditModal(this.closest('tr'))">Edit</button>
                                    <form method="POST" action="{{ route('manufacturers.destroy', $manufacturer) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this manufacturer?');">
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
            <div class="pagination">{{ $manufacturers->links() }}</div>
        @else
            <div class="empty-state">
                <p>No manufacturers found.</p>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('createModal').classList.add('active')">Add Your First Manufacturer</button>
            </div>
        @endif
    </div>
</div>

<div id="createModal" class="modal" onclick="if (event.target === this) this.classList.remove('active')">
    <div class="modal-content">
        <div class="modal-header"><h2>Add New Manufacturer</h2></div>
        <form method="POST" action="{{ route('manufacturers.store') }}">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('createModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="modal" onclick="if (event.target === this) this.classList.remove('active')">
    <div class="modal-content">
        <div class="modal-header"><h2>Edit Manufacturer</h2></div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_name">Name</label>
                <input type="text" id="edit_name" name="name" required>
                @error('name')<div class="error">{{ $message }}</div>@enderror
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
    document.getElementById('edit_name').value = row.getAttribute('data-name') || '';
    document.getElementById('editForm').action = row.getAttribute('data-update-url');
    document.getElementById('editModal').classList.add('active');
}
</script>
@endpush
