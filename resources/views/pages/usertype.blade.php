@extends('layout')

@section('content')

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
                    <tr>
                        <td class="role-name"><strong>{{ $role->name }}</strong></td>
                        <td>{{ $role->created_by ?? 'System' }}</td>
                        <td>{{ $role->updated_by ?? 'N/A' }}</td>
                        <td>{{ $role->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-primary dropdown-toggle" 
        type="button" 
        data-bs-toggle="dropdown" 
        data-bs-display="static"> Action
</button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item edit-type" href="javascript:void(0)" data-id="{{ $role->id }}"><i class="bi bi-pencil-square me-2"></i> Edit</a></li>
                                    <li><button class="dropdown-item text-danger delete-type" data-id="{{ $role->id }}"><i class="bi bi-trash me-2"></i> Delete</button></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
   $(document).ready(function() {
    
    // --- 1. SEARCH FUNCTIONALITY ---
    $("#typeSearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#userTypeTableBody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
function applyPagination() {
    var limit = parseInt($('#entriesPerPage').val());
    var $rows = $("#userTypeTableBody tr");
    $rows.hide();
    $rows.slice(0, limit).show();
}
$('#entriesPerPage').on('change', applyPagination);
applyPagination();
    // --- 3. MODAL CONTROLS ---
    $('#openAddTypePopup').click(function() { 
        $('#addTypeModal').fadeIn(200); 
    });

    $('#closeAddType').click(function() { 
        $('#addTypeModal').fadeOut(200); 
    });

    $(window).click(function(event) {
        if (event.target.id == 'addTypeModal') {
            $('#addTypeModal').fadeOut(200);
        }
    });

    $('#add_type_form').off('submit').on('submit', function(e) {
        e.preventDefault();

        const $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('addusertype') }}", 
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                alert("Role added successfully!");
                location.reload();
            },
            error: function(xhr) {
                $submitBtn.prop('disabled', false).text('Save Role');
                let errorMsg = xhr.responseJSON.errors?.user_type ? 
                               xhr.responseJSON.errors.user_type[0] : 
                               "Check if role already exists.";
                alert("Error: " + errorMsg);
            }
        });
    });
    $(document).on('shown.bs.dropdown', '.dropdown', function () {
        const $button = $(this).find('.dropdown-toggle');
        const $menu = $(this).find('.dropdown-menu');
        $('body').append($menu);
        
        const offset = $button.offset();
        
        $menu.css({
            'display': 'block',
            'position': 'absolute',
            'top': (offset.top + $button.outerHeight()) + 'px',
            'left': offset.left + 'px',
            'width': 'auto',
            'z-index': 10000
        });
    });

    $(document).on('hide.bs.dropdown', '.dropdown', function () {
        const $menu = $('body > .dropdown-menu');
        $(this).append($menu);
        $menu.css({
            'display': 'none',
            'position': '',
            'top': '',
            'left': '',
            'z-index': ''
        });
    });    
    $(document).on('click', '.edit-type', function() {
        let id = $(this).data('id');
        $.get('/usertype/' + id + '/edit', function(data) {
            $('#edit_type_id').val(data.id);
            $('#edit_type_name').val(data.name);
            $('#editTypeModal').fadeIn(200);
        });
    });
    $(document).on('click', '.delete-type', function() {
        if (confirm("Are you sure you want to delete this role?")) {
            let id = $(this).data('id');
            
            $.ajax({
                url: '/usertype/' + id,
                type: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    alert(response.success);
                    location.reload();
                },
                error: function() { alert("Error deleting role."); }
            });
        }
    });

    $('#closeEditType').click(function() { $('#editTypeModal').fadeOut(200); });
// --- 6. EDIT FUNCTIONALITY ---

// Step A: Open Modal and Populate Data
$(document).on('click', '.edit-type', function() {
    let id = $(this).data('id'); // Gets the ID from data-id="{{ $role->id }}"
    
    // Clear previous input just in case
    $('#edit_type_name').val('Loading...');

    // Fetch current data from your Laravel Route
    // This expects a route like: Route::get('/usertype/{id}/edit', [Controller::class, 'edit']);
    $.get('/usertype/' + id + '/edit', function(data) {
        $('#edit_type_id').val(data.id);       // Hidden input for the ID
        $('#edit_type_name').val(data.name);  // Input for the Role Name
        $('#editTypeModal').fadeIn(200);      // Show the modal
    }).fail(function() {
        alert("Could not fetch data. Please check if the route exists.");
    });
});

// Step B: Submit the Edit Form
$('#edit_type_form').on('submit', function(e) {
    e.preventDefault();
    
    let id = $('#edit_type_id').val();
    const $submitBtn = $(this).find('button[type="submit"]');
    
    $submitBtn.prop('disabled', true).text('Updating...');

    $.ajax({
        // This matches Route::put('/usertype/{id}', [Controller::class, 'update']);
        url: '/usertype/' + id,
        type: 'POST', // Use POST because we are using @method('PUT') in the form
        data: $(this).serialize(),
        success: function(response) {
            alert(response.success || "Role updated successfully!");
            location.reload(); // Refresh to show changes
        },
        error: function(xhr) {
            $submitBtn.prop('disabled', false).text('Update Role');
            alert("Error: " + (xhr.responseJSON.message || "Update failed."));
        }
    });
});

// Step C: Close Modal
$('#closeEditType').click(function() { 
    $('#editTypeModal').fadeOut(200); 
});

});
    </script>
@endsection