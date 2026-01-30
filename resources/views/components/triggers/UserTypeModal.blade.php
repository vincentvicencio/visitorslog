<!-- //////////////////////////////////////////////     MODALS       ////////////////////////////////////////////////////// -->
<!-- Add -->
 <div id="addTypeModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
    <div style="background:white; width:350px; margin:100px auto; padding:20px; border-radius:8px; position:relative;">
        <button id="closeAddType" type="button" class="btn-close" style="float:right; border:none;" aria-label="Close"></button>
        <div class="header fs-4 fw-bold mb-0">Add New User Type</div>
        <div class="subheader mb-3">Create and register a new user type</div>
        <form id="add_type_form">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold mb-0">Role Name</label>
                <input type="text" name="user_type" class="form-control mb-4" placeholder="e.g. Administrator" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Add Role</button>
        </form>
    </div>
</div>

<!-- edit -->
<div id="editTypeModal"  class="position-fixed top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.5); z-index: 10000; display:none">
    <div class="bg-white mx-auto position-relative p-4 rounded-3"style="width: 400px;margin:100px auto;">
        <button id="closeEditType" type="button" class="btn-close" style="float:right; border:none;" aria-label="Close"></button>
        <div class="header fs-4 fw-bold mb-0">Edit User Type</div>
        <div class="subheader mb-3">Update and modify an existing user type</div>
        <form id="edit_type_form">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_type_id">
            <div class="mb-3">
                <label class="form-label fw-bold mb-0">Role Name</label>
                <input type="text" id="edit_type_name" name="user_type" class="form-control mb-4" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Role</button>
        </form>
    </div>
</div>

<!--  -->
<div class="modal fade position-fixed top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.5); z-index: 10000; display:none" id="deleteRoleModal">
    <div class="modal-dialog bg-white position-relative  rounded-3" style="width: 400px;margin:100px auto;">
        <div class="modal-content p-4">
            <button  type="button" class="btn-close position-absolute top-0 end-0 m-3" style=" border:none;" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="header fs-4 fw-bold mb-0">Delete User Type</div>
            <div class="subheader mb-3">This will permanently delete the selected user type</div>

            <div class="fs-5 delete-message">
                Are you sure you want to delete this role? This will hide it from the selection list.
            </div>
            <div class="delete-button">
                <button type="button" class="btn btn-secondary cancel-button" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteRoleBtn delete-button" class="btn btn-danger">Delete Role</button>
            </div>
        </div>
    </div>
</div>

<!-- 
<div class="modal fade position-fixed top-0 start-0 w-100 h-100" style=" z-index: 10000; display:none" id="deleteRoleModal">
    <div class="modal-dialog bg-white position-relative p-4 rounded-3" style="width: 400px;margin:100px auto;">
        <div class="modal-content">
            <button  type="button" class="btn-close" style="float:right; border:none;" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="header fs-4 fw-bold mb-0">Delete User Type</div>
            <div class="subheader mb-3">This will permanently delete the selected user type</div>

            <div class="">
                Are you sure you want to delete this role? This will hide it from the selection list.
            </div>
            <div class="">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteRoleBtn" class="btn btn-danger">Delete Role</button>
            </div>
        </div>
    </div>
</div> -->