
<!-- //////////////////////////////////////////////     USERS MODALS       ////////////////////////////////////////////////////// -->
<!-- <div id="registeruserpopup" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
    <div style="background:white; width:350px; margin:100px auto; padding:20px; border-radius:8px; position:relative;">
        <button id="close_register_user_popup" type="button" style="float:right;">X</button>
        
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
</div> -->


<div class="modal fade" id="registerUserModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">Register User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reg_user_db_id"> 
                
                <div class="mb-3">
                    <label class="form-label">Select Role</label>
                    <select id="reg_user_type" class="form-control" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Select Location</label>
                    <select id="reg_location" class="form-control" required>
                        <option value="">Select Location</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Employee Code</label>
                    <input type="text" id="reg_emp_code" class="form-control" placeholder="Employee Code" required>
                </div>

                <div class="mb-3" id="password_container">
                    <label class="form-label">Password</label>
                    <input type="password" id="reg_password" class="form-control" placeholder="Password">
                    <small class="text-muted edit-only-text" style="display:none;">Leave blank to keep current password</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary w-100" id="submit_user_btn">Register User</button>
            </div>
        </div>
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


<!-- <div class="modal fade text-center" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
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
</div> -->