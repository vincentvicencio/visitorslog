
<!-- //////////////////////////////////////////////     USERS MODALS       ////////////////////////////////////////////////////// -->
<div id="registeruserpopup" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
    <div style="background:white; width:350px; margin:100px auto; padding:20px; border-radius:8px; position:relative;">
        <button id="close_register_user_popup" type="button" class="btn-close" style="float:right; border:none;" aria-label="Close"></button>
        <div class="header fs-4 fw-bold mb-0">Add New User Type</div>
        <div class="subheader mb-3">Create and register a new user type</div>
        
            <form id="registered_user_form">
            @csrf
            <select name="user_type" id="reg_user_type" class="form-control" required>
                <option value="">Select Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>

            <select name="locations" id="reg_location" class="form-control my-2" required>
                <option value="">Select Location</option>
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