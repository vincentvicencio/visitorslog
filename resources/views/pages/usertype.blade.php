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


@vite (['resources/js/usertype.js'])
@include('components.triggers.toast')

<style>
    .visitor-log-sheet-table {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        background-color: white;
        overflow: visible !important; 
    }
    .table-scroll-container {
        position: relative;
        overflow: visible !important; 
        padding-bottom: 20px;
    }
    #usertypesTable thead th {
        background-color: #003366;
        color: white;
        padding: 12px;
        border-bottom: 2px solid #002244;
    }

    #usertypesTable {
        margin-bottom: 0;
        width: 100% !important;
    }

    /* Hover effect for rows */
    #userTypeTableBody tr:hover {
        background-color: #f8f9fa;
    }
</style>


<div class="user-types-container mt-4">
    <div class="page-header">
        <div class="header-content">
            <div class="page-title fs-2">User Types</div>
            <div class="page-subtitle mb-3">Manage and organize different user roles</div>
        </div>
            <button type="button" id="openAddTypePopup" class="top-button">Add User Type</button>
    </div>

    <div class="visitor-log-sheet-table bg-white">
        <div class="search-field d-flex align-items-center justify-content-between p-3 border-bottom">

        <div class="search-field d-flex align-items-center justify-content-between">
            search
             <input type="text" id="typeSearch" class="flex-grow-1 mx-2" placeholder="Search">
            entries per page
            <select name="" id="entriesPerPage" class="number-per-page form-select-sm">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
        </div>

        <div class="table-scroll-container">
            <table class="table table-bordered align-middle" id="usertypesTable">
                <thead>
                    <tr class="table-header">
                        <th>Name</th>
                        <th>Created by</th>
                        <th>Updated by</th>
                        <th>Created date</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="userTypeTableBody">
                    @forelse($roles as $role)
                        @if(is_null($role->deleted_at))
                            <tr>
                                <td class="role-name"><strong>{{ $role->name }}</strong></td>
                                <td>{{ $role->created_by ?? 'System' }}</td>
                                <td>{{ $role->updated_by ?? 'N/A' }}</td>
                                <td>{{ $role->created_at->format('Y-m-d') }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <!-- <button class="btn btn-sm btn-primary dropdown-toggle" 
                                                type="button" 
                                                data-bs-toggle="dropdown" 
                                                data-bs-display="static"> Action
                                        </button> -->
                                        <button class="btn btn-sm btn-primary dropdown-toggle" 
                                                type="button" 
                                                data-bs-toggle="dropdown" 
                                                data-bs-boundary="viewport" aria-expanded="false">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item edit-type" href="javascript:void(0)" data-id="{{ $role->id }}"><i class="bi bi-pencil-square me-2"></i> Edit</a></li>
                                            <li><button class="dropdown-item text-danger delete-type" data-id="{{ $role->id }}"><i class="bi bi-trash me-2"></i> Delete</button></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">No roles found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <!-- Pagination -->
            <x-table-pagination/>
        </div>
        </div>
    </div>
</div>

<div id="addTypeModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10000;">
    <div style="background:white; width:400px; margin:100px auto; padding:25px; border-radius:12px; position:relative;">
        <button id="closeAddType" type="button" class="btn-close" style="float:right; border:none; background:none;">X</button>
        <h4 class="mb-4">Add New User Type</h4>
        <form id="add_type_form">
            @csrf
            <div class="mb-3">
                <label class="form-label">Role Name</label>
                <input type="text" name="user_type" class="form-control" placeholder="e.g. Administrator" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Save Role</button>
        </form>
    </div>
</div>

<div id="editTypeModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10000;">
    <div style="background:white; width:400px; margin:100px auto; padding:25px; border-radius:12px; position:relative;">
        <button id="closeEditType" type="button" class="btn-close" style="float:right; border:none; background:none;">X</button>
        <h4 class="mb-4">Edit User Type</h4>
        <form id="edit_type_form">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_type_id">
            <div class="mb-3">
                <label class="form-label">Role Name</label>
                <input type="text" id="edit_type_name" name="user_type" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Role</button>
        </form>
    </div>
</div>


<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteRoleLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this role? This will hide it from the selection list.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteRoleBtn" class="btn btn-danger">Delete Role</button>
            </div>
        </div>
    </div>
</div>

@endsection


