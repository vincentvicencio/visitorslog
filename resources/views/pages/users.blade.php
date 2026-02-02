@extends('layout')

@section('content')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    window.Laravel = {
        baseUrl: "{{ url('/') }}",
        csrfToken: "{{ csrf_token() }}" // This correctly gets the token from Laravel
    };
</script>

@vite(['resources/js/users.js'])
@vite(['resources/js/try.js'])
@include('components.triggers.users-userstype-toast')
@include('components.triggers.UsersModal')

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
        <!-- <button type="button" id="openPopup2" class="top-button">Register User</button> -->
             {{-- <button type="button" id="register_btn" class="top-button">Register User</button> --}}
    </div>
    <!-- table.scss -->
    <div class="visitor-log-sheet-table table-responsive-sm table-responsive-md table-responsive-lg bg-white">
        <div class="search-field d-flex align-items-center justify-content-between w-100 rounded-3 mb-2 fw-bold fs-6 text-primary-custom ps-2 small-caps">
            search
            <input type="text" id="tableSearch" placeholder="search" class="flex-grow-1 mx-2 border-0 rounded-2 ms-2 me-4 ps-3">
            entries per page
            <select name="" id="entriesPerPage" class="number-per-page form-select-sm">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
        <!-- Table -->
        <table class="table table-bordered align-middle" id="userTable">
            <thead>
                <tr class="table-header">
                    <th>Username</th>
                    <th>Role</th>
                    <th>Created by</th>
                    <th>Updated by</th>
                    <th>Created date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="employeeTableBody">
            @forelse($registeredUsers as $user)
    <tr>
        <td>{{ $user->first_name }}</td>
         <td>{{ $user->userType->name ?? 'None' }}</td>
        <!-- <td>{{ $user->created_by }}</td>
        <td>{{ $user->updated_by}}</td> -->
        <td>{{ $user->getEmpName($user->created_by) }}</td>
        <td>{{ $user->getEmpName($user->updated_by) }}</td>
        <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
        <td class="text-center">
            <div class="dropdown">
                    <button class="btn btn-sm btn-primary dropdown-toggle" 
                        type="button" 
                        data-bs-toggle="dropdown" 
                        data-bs-boundary="viewport" aria-expanded="false">
                    Action
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item edit-user" href="javascript:void(0)" 
                        data-id="{{ $user->id }}">
                            <i class="bi bi-pencil-square me-2"></i> Edit
                        </a>
                    </li>
                <li>
                    <button type="button" class="dropdown-item text-danger delete-user" 
                            data-id="{{ $user->id }}">
                        <i class="bi bi-trash me-2"></i> Delete
                    </button>
                </li>
                </ul>
            </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No employees registered yet.</td>
        </tr>
        @endforelse
            </tbody>
        </table>
            <!-- Pagination -->
            <x-table-pagination/>
    </div>
</div>


<!-- <div class="container mt-4">
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
</div> -->

@endsection