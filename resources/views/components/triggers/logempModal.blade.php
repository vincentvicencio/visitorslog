<div class="modal fade" id="logempModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logempModalTitle">Log Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="logemp_form">
                    @csrf
                <input type="hidden" id="logemp_db_id" name="record_id"> 
                    <div class="mb-3" id="emp_code_container">
                        <div class="input-group">
                            <input type="text" id="logemp_emp_code" name="logemp_code" class="form-control" placeholder="Enter Employee Code or Name" autocomplete="off" aria-describedby="empCodeFeedback">
                            <button class="btn btn-outline-primary" type="button" id="search_emp_btn">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="empCodeFeedback">Employee Code is required</div>
                    </div>
                    <div class="mb-3" id="searched_emp_code_container" style="display:none;">
                        <label class="form-label">Searched Employee Code or Name</label>
                        <input type="text" id="searched_emp_code" class="form-control" readonly>
                    </div>
                    <div class="mb-3 d-none" id="employee_name_container">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">First Name</label>
                                <input type="text" id="logemp_first_name" name="logemp_first_name" class="form-control" autocomplete="off" aria-describedby="firstNameFeedback">
                                <div class="invalid-feedback" id="firstNameFeedback">First Name is required</div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" id="logemp_last_name" name="logemp_last_name" class="form-control" autocomplete="off" aria-describedby="lastNameFeedback">
                                <div class="invalid-feedback" id="lastNameFeedback">Last Name is required</div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary w-100" id="submit_logemp_btn">Log Employee</button>
            </form>
            </div>
        </div>
    </div>
</div>

 