@extends('layout')



@include('components.triggers.users-userstype-toast')
@include('components.triggers.UsersModal')

@section('content')

@vite(['resources/js/users.js'])
@vite(['resources/js/UsersClass.js'])   

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    window.Laravel = {
        baseUrl: "{{ url('/') }}",
        csrfToken: "{{ csrf_token() }}"
    };
</script>



<div class="user-types-container mt-4">
    <div class="page-header">
        <div class="header-content">
            <div class="page-title fs-2">Users</div>
            <div class="page-subtitle mb-3">Manage and organize user accounts and their details</div>
        </div>
        <div class="top-button position-absolute top-50 end-0 translate-middle-y d-flex align-items-center justify-content-center
         text-white rounded-2 border-0 cursor-pointer px-3 py-2" id="register_btn">
            Register User
        </div>
    </div>
    <!-- table.scss -->
    <div class="visitor-log-sheet-table table-responsive-sm table-responsive-md table-responsive-lg bg-white">
        <!-- search and filter -->
        <x-table-filter/>
    
        <table class="table table-bordered align-middle" id="usersTable">
            <thead></thead>
        </table>

        <!-- Pagination -->
        <x-table-pagination/>
    </div>
</div>
 <div class="container mt-4">
    <button class="btn btn-outline-info btn-sm mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#sessionEmployeeTable" aria-expanded="false" aria-controls="sessionEmployeeTable">
        ...
    </button>

    <div class="collapse" id="sessionEmployeeTable">
        <div class="card card-body">
            <h3>Available Employees (From Central Hub Session)</h3>
            <table class="table table-bordered table-sm">
                <thead class="table-secondary">
                    <tr>
                        <th>Emp Code</th>
                        <th>Full Name</th>
                        <th>Dept ID</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allEmployeesFromSession as $emp)
                        <tr>
                            <td>{{ $emp['emp_code'] ?? 'N/A' }}</td>
                            <td>{{ ($emp['first_name'] ?? '') . ' ' . ($emp['last_name'] ?? '') }}</td>
                            <td>{{ $emp['department_id'] ?? 'N/A' }}</td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="copyToRegister('{{ $emp['emp_code'] }}')">Select</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No session data found. Please re-login.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div> 

@endsection