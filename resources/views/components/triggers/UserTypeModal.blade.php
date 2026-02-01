<!-- //////////////////////////////////////////////     MODALS       ////////////////////////////////////////////////////// -->
<!-- <div id="addTypeModal" class="modal fade" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10000;" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="textInputModalLabel" aria-hidden="true">
    <div style="background:white; width:400px; margin:100px auto; padding:25px; border-radius:12px; position:relative;">
        <button id="closeAddType" type="button" class="btn-close" style="float:right; border:none; background:none;">X</button>
        <h4 class="mb-4">User Type</h4>
        <form id="add_type_form">
            @csrf
            <div class="mb-3">
                <label class="form-label">Role Name</label>
                <input type="hidden" id="edit_type_id">
                <input type="text" name="user_type" class="form-control" placeholder="e.g. Administrator" required id='edit_type_name'>
            </div>
            <button type="submit" class="btn btn-primary w-100" id="save_type"></button>
        </form>
    </div>
</div>   -->

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

<!-- <div id="editTypeModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10000;">
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
</div>-->


<!-- <div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleLabel" aria-hidden="true">
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
</div>  -->
