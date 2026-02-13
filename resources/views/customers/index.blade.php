@extends('layouts.app')

@section('title', 'Customers - ' . config('app.name'))

@section('content')
<div class="container" style="max-width: 1600px;">
    <div class="page-header">
        <h1>Customers</h1>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('addCustomerModal').classList.add('active')">Add Customer</button>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error') || $errors->any())
        <div class="alert-danger">
            {{ session('error') }}
            @foreach($errors->all() as $err) {{ $err }} @endforeach
        </div>
    @endif

    <div class="card" style="overflow-x: auto;">
        @if($customers->count() > 0)
            <table class="table" style="min-width: 1100px;">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Surname</th>
                        <th>Company</th>
                        <th>Email</th>
                        <th>Cell</th>
                        <th>Tel</th>
                        <th>ID Nr</th>
                        <th>Date of Birth</th>
                        <th>Handset Preference</th>
                        <th>Source</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                        <tr onclick="window.location='{{ route('customers.show', $customer) }}';">
                            <td>{{ $customer->name ?? '—' }}</td>
                            <td>{{ $customer->surname ?? '—' }}</td>
                            <td>{{ $customer->company_name ?? '—' }}</td>
                            <td>{{ $customer->email ?? '—' }}</td>
                            <td>{{ $customer->cell ?? '—' }}</td>
                            <td>{{ $customer->tel ?? '—' }}</td>
                            <td>{{ $customer->ID_nr ?? '—' }}</td>
                            <td>{{ $customer->date_of_birth ? $customer->date_of_birth->format('Y-m-d') : '—' }}</td>
                            <td>{{ $customer->handset_preference ?? '—' }}</td>
                            <td>{{ $customer->source ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $customers->links() }}</div>
        @else
            <div class="empty-state">
                <p>No customers found.</p>
            </div>
        @endif
    </div>
</div>

<div id="addCustomerModal" class="modal" onclick="if (event.target === this) this.classList.remove('active')">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Customer</h2>
        </div>
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="customer_name">Name</label>
                    <input type="text" id="customer_name" name="name" value="{{ old('name') }}">
                    @error('name')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="customer_surname">Surname</label>
                    <input type="text" id="customer_surname" name="surname" value="{{ old('surname') }}">
                    @error('surname')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group">
                <label for="customer_company_name">Company name</label>
                <input type="text" id="customer_company_name" name="company_name" value="{{ old('company_name') }}">
                @error('company_name')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="customer_email">Email</label>
                    <input type="email" id="customer_email" name="email" value="{{ old('email') }}">
                    @error('email')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="customer_cell">Cell</label>
                    <input type="text" id="customer_cell" name="cell" value="{{ old('cell') }}">
                    @error('cell')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="customer_tel">Tel</label>
                    <input type="text" id="customer_tel" name="tel" value="{{ old('tel') }}">
                    @error('tel')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="customer_ID_nr">ID Nr</label>
                    <input type="text" id="customer_ID_nr" name="ID_nr" value="{{ old('ID_nr') }}">
                    @error('ID_nr')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="customer_date_of_birth">Date of birth</label>
                    <input type="date" id="customer_date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                    @error('date_of_birth')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="customer_handset_preference">Handset preference</label>
                    <input type="text" id="customer_handset_preference" name="handset_preference" value="{{ old('handset_preference') }}">
                    @error('handset_preference')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group">
                <label for="customer_source">Source</label>
                <input type="text" id="customer_source" name="source" value="{{ old('source') }}">
                @error('source')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addCustomerModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
@if($errors->any())
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addCustomerModal').classList.add('active');
});
@endif
</script>
@endpush
