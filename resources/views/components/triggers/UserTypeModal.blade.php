<div class="modal fade" id="addTypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">User Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeAddType"></button>
            </div>
            <div class="modal-body">
                <form id="add_type_form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="user_type" class="form-control" placeholder="e.g. Administrator" required id='edit_type_name'>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="add_type_form" class="btn btn-primary w-100" id="save_type">Save</button>
            </div>
        </div>
    </div>
</div>
