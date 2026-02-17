<div class="modal fade" id="addTypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">User Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeAddType"></button>
            </div>
            <div class="modal-body">
                <form id="add_type_form">
                    <div class="mb-3">
                        <input hidden="hidden" name="record_id" id="record_id">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Administrator" required id='name'>
                        <span class="error-span error-name text-danger"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary w-100" id="btn_submit">Save</button>
            </div>
        </div>
    </div>
</div>
