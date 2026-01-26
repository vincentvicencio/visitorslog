@extends('layout')

@section('content')
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
    .table-scroll-container {
        position: relative;
        overflow: visible !important;
    }

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
         <div class="table-scroll-container">
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
        <td>{{ $user->user_type }}</td>
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
    <select name="user_type" id="reg_user_type" class="form-control" required>
    <option value="">Select Role</option>
    @foreach($roles as $role)
        <option value="{{ $role->id }}">{{ $role->name }}</option>
    @endforeach
</select>

<input type="text" id="reg_emp_code" name="emp_code" placeholder="Employee Code" class="form-control" required>
<input type="text" id="reg_loc" name="reg_loc" placeholder="Location" class="form-control" required>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $(document).on('click', '.dropdown-toggle', function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        const menu = $(this).next('.dropdown-menu');
        $('.dropdown-menu').not(menu).removeClass('show');
        menu.toggleClass('show');
    });
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });
    function updateTableRows() {
        var limit = parseInt($('#entriesPerPage').val()); 
        var $rows = $('#employeeTableBody tr');
        $rows.hide();
        $rows.slice(0, limit).show();

        console.log("Showing " + limit + " rows");
    }
    updateTableRows();

    $('#entriesPerPage').on('change', function() {
        updateTableRows();
    });

    $("#tableSearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        var $rows = $("#employeeTableBody tr");

        if (value === "") {
            updateTableRows(); 
        } else {
            $rows.filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        }
    });

    $('#openPopup2').click(function() { $('#popupContainer2').fadeIn(); });
$('#closePopup2').click(function() { $('#popupContainer2').fadeOut(); });

// Handle AJAX Submission
$('#registered_user_form').on('submit', function(e) {
    e.preventDefault(); 

    $.ajax({
        url: "{{ route('addusers') }}", 
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            // Update these to match the new IDs we just created
            emp_code: $('#reg_emp_code').val(),
            password: $('#reg_password').val(),
            user_type: $('#reg_user_type').val() 
        },
        success: function(response) {
            alert(response.message);
            location.reload();
        },
        error: function(xhr) {
            // This will now show you the specific validation error from Laravel
            console.log(xhr.responseText);
            alert("Validation Error: " + xhr.responseJSON.message);
        }
    });
});

function copyToRegister(code) {
    // Fill the employee code input in your existing registration form
    $('#reg_emp_code').val(code);
    
    // Open the registration popup (popupContainer2)
    $('#popupContainer2').fadeIn();
}

$(document).on('click', '.delete-user', function() {
    var userId = $(this).data('id');
    var row = $(this).closest('tr'); // Capture the row to remove it later

    if (confirm("Are you sure you want to delete this user?")) {
        $.ajax({
            url: "/delete-user/" + userId, // You will need to create this route
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                alert("User deleted successfully!");
                row.fadeOut(); // Smoothly remove the row from the table
            },
            error: function(xhr) {
                alert("Error deleting user.");
            }
        });
    }
});


// 1. Open Edit Popup and Load Data
$(document).on('click', '.edit-user', function() {
    var userId = $(this).data('id');
    
    $.get("/get-user/" + userId, function(data) {
        $('#edit_user_id').val(data.id);
        $('#edit_user_type').val(data.role_id); // Adjust based on your DB column name
        $('#edit_emp_code').val(data.emp_code);
        $('#editPopupContainer').fadeIn();
    });
});

// 2. Close Edit Popup
$('#closeEditPopup').click(function() { $('#editPopupContainer').fadeOut(); });
$('#edit_user_form').on('submit', function(e) {
    e.preventDefault();
    
    // Get the ID from your hidden input field in the edit modal
    var userId = $('#edit_user_id').val(); 

    $.ajax({
        // This MUST match the route: /update-user/{id}
        url: "/update-user/" + userId, 
        type: "POST",
        data: $(this).serialize(), // This sends _token and your form inputs
        success: function(response) {
            alert(response.message);
            location.reload();
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert("Error: " + (xhr.responseJSON ? xhr.responseJSON.message : "Update failed"));
        }
    });
});
});
</script>
@endsection