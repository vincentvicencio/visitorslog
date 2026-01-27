@extends('layout')

@section('content')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    window.Laravel = {
        baseUrl: "{{ url('/') }}",
        csrfToken: "{{ csrf_token() }}" // This correctly gets the token from Laravel
    };
</script>

@vite(['resources/js/users.js'])

<style>
    .visitor-log-sheet-table {
        width: 100%;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background-color: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        overflow: visible !important;
    }

    .search-field {
        padding: 15px;
        border-bottom: 1px solid #eee;
    }
    /* .table-scroll-container {
        position: relative;
        overflow: visible !important;
    } */

    #userTable {
        width: 100% !important;
        margin-bottom: 0;
        table-layout: auto;
    }

    #userTable thead th {
        background-color: #003366; 
        color: white;
        padding: 12px;
    }
    .dropdown {
        position: relative;
    }
    
    .dropdown-menu {
        z-index: 9999;
    }
</style>
<div class="user-types-container mt-4">
    <div class="page-header">
        <div class="header-content">
            <div class="page-title fs-2">Users</div>
            <div class="page-subtitle mb-3">Manage and organize user accounts and their details</div>
        </div>
             <button type="button" id="openPopup2" class="top-button">Register User</button>
    </div>
    <!-- table.scss -->
    <div class="visitor-log-sheet-table table-responsive-sm table-responsive-md table-responsive-lg bg-white">
        <div class="search-field d-flex align-items-center justify-content-between">
            search
             <input type="text" id="tableSearch" class="flex-grow-1 mx-2" placeholder="Search">
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
        <td>{{ $user->created_by }}</td>
        <td>{{ $user->updated_by}}</td>
        <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
        <td class="text-center">
                        <div class="dropdown"> 
                <button class="btn btn-sm btn-primary dropdown-toggle" 
            type="button" 
            data-bs-toggle="dropdown" 
            aria-expanded="false">
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
            </div>
            <!-- Pagination -->
            <x-table-pagination/>
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

<div id="popupContainer2" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
    <div style="background:white; width:350px; margin:100px auto; padding:20px; border-radius:8px; position:relative;">
        <button id="closePopup2" type="button" style="float:right;">X</button>
        
            <form id="registered_user_form">
            @csrf
            <p>Total Roles Found: {{ count($roles)}}</p>
            <select name="user_type" id="reg_user_type" class="form-control" required>
                <option value="">Select Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>

            <input type="text" id="reg_emp_code" name="emp_code" placeholder="Employee Code" class="form-control" required>
            <input type="password" id="reg_password" name="password" placeholder="Password" class="form-control" required>

            <button type="submit" class="btn btn-primary">Register User</button>
        </form>
    </div>
</div>


<div id="editPopupContainer" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
    <div style="background:white; width:350px; margin:100px auto; padding:20px; border-radius:8px; position:relative;">
        <button id="closeEditPopup" type="button" style="float:right;">X</button>
        <h4>Edit User</h4>
        <form id="edit_user_form">
            @csrf
            <input type="hidden" id="edit_user_id" name="id">
            
            <label>Role</label>
            <select name="user_type" id="edit_user_type" class="form-control mb-2" required>
                <option value="">Select Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>

            <label>Employee Code</label>
            <input type="text" id="edit_emp_code" name="emp_code" class="form-control mb-3" required>

            <button type="submit" class="btn btn-success w-100">Update User</button>
        </form>
    </div>
</div>

<div class="modal fade text-center" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this user? This action will deactivate the account.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>


<div class="toast-container position-fixed top-0 end-0 p-3">
  <div id="deletesuccessToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toastMessage">
        User Deleted Successfully!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3">
  <div id="editsuccessToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toastMessage">
        User Updated Successfully!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3">
  <div id="addsuccessToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toastMessage">
        User Added Successfully!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
@endsection