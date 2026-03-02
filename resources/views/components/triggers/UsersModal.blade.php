<div class="modal fade" id="registerUserModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">Register User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="register_user_form">
                    @csrf
                <input type="hidden" id="reg_user_db_id" name="record_id"> 
                
                <div class="mb-3">
                    <label class="form-label">Select Role</label>
                    <select id="reg_user_type" name="user_type" class="form-control" required  autocomplete="off" aria-describedby="roleFeedback">
                    </select>
                    <div class="invalid-feedback" id="roleFeedback">Role is required</div>
                </div>

                <div id="reg_fields_container" style="display: none;">
                    <div class="mb-3" id="location_container">
                        <label class="form-label">Select Location</label>
                        <select id="reg_location" name="locations[]" class="form-control" autocomplete="off" aria-describedby="locationFeedback">
                            <option value="">Select Location</option>
                        </select>
                        <div class="invalid-feedback" id="locationFeedback">Location is required</div>
                    </div>
                    <div class="mb-3" id="emp_code_container">
                        <label class="form-label">Employee Code</label>
                        <div class="input-group">
                            <input type="text" id="reg_emp_code" name="emp_code" class="form-control" placeholder="Enter Employee Code" autocomplete="off" aria-describedby="empCodeFeedback">
                            <button class="btn btn-outline-primary" type="button" id="search_emp_btn">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="empCodeFeedback">Employee Code is required</div>
                    </div>
                    <div class="mb-3" id="searched_emp_code_container" style="display:none;">
                        <label class="form-label">Searched Employee Code</label>
                        <input type="text" id="searched_emp_code" class="form-control" readonly>
                    </div>
                    <div class="mb-3 d-none" id="employee_name_container">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">First Name</label>
                                <input type="text" id="reg_first_name" name="first_name" class="form-control" autocomplete="off" aria-describedby="firstNameFeedback">
                                <div class="invalid-feedback" id="firstNameFeedback">First Name is required</div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" id="reg_last_name" name="last_name" class="form-control" autocomplete="off" aria-describedby="lastNameFeedback">
                                <div class="invalid-feedback" id="lastNameFeedback">Last Name is required</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3" id="password_container">
                        <label class="form-label">Password</label>
                        <input type="password" id="reg_password" name="password" class="form-control" placeholder="Password" required autocomplete="off" aria-describedby="passwordFeedback">
                        <div class="invalid-feedback" id="passwordFeedback">Password is required</div>
                        <small class="text-muted edit-only-text" style="display:none;"> Leave blank to keep current password </small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary w-100" id="submit_user_btn">Register User</button>
            </form>
            </div>
        </div>
    </div>
</div>

 