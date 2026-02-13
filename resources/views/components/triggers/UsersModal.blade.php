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

                <div id="reg_fields_container" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label">Select Location</label>
                        <select id="reg_location" class="form-control" required>
                            <option value="">Select Location</option>
                        </select>
                    </div>
                    <div class="mb-3" id="emp_code_container">
                        <label class="form-label">Employee Code</label>
                        <div class="input-group">
                            <input type="text" id="reg_emp_code" class="form-control" placeholder="Enter Employee Code" autocomplete="off">
                            <button class="btn btn-outline-primary" type="button" id="search_emp_btn">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3" id="employee_name_container" style="display: none;">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">First Name</label>
                                <input type="text" id="reg_first_name" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" id="reg_last_name" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3" id="password_container">
                        <label class="form-label">Password</label>
                        <input type="password" id="reg_password" class="form-control" placeholder="Password">
                        <small class="text-muted edit-only-text" style="display:none;">Leave blank to keep current password</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary w-100" id="submit_user_btn">Register User</button>
            </div>
        </div>
    </div>
</div>

{{-- Edit User Type --}}
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