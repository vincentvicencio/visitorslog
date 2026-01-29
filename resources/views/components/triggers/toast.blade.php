<!-- //////////////////////////////////////////     USER TOASTS      /////////////////////////////////////////////// -->
<div class="toast-container top-0 end-0 p-3" id="toast-container">
    <div id="edit_user_successToast" class="toast position-fixed top-0 end-0 my-4 mx-3 text-white bg-success border-0 " role="alert" aria-live="assertive" aria-atomic="true"
        data-bs-delay="1000">
        <div class="toast-header">
            <strong class="me-auto"><i class="bi bi-bell-fill"></i> &nbsp; <span class="toast-title"></span></strong>
            <small>&nbsp;</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">
        <strong>User Updated Successfully!</strong>
        </div>
    </div>
</div>

<div class="toast-container top-0 end-0 p-3">
    <div  id="delete_user_successToast"  class="toast position-fixed top-0 end-0 my-4 mx-3 text-black bg-danger border-0 " role="alert" aria-live="assertive" aria-atomic="true"
        data-bs-delay="1000">
        <div class="toast-header">
            <strong class="me-auto"><i class="bi bi-bell-fill"></i> &nbsp; <span class="toast-title"></span></strong>
            <small>&nbsp;</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">
        <strong>User Deleted Successfully!</strong>
        </div>
    </div>
</div>


<div class="toast-container top-0 end-0 p-3">
    <div  id="add_user_successToast"  class="toast position-fixed top-0 end-0 my-4 mx-3 text-white bg-success border-0 " role="alert" aria-live="assertive" aria-atomic="true"
        data-bs-delay="1000">
        <div class="toast-header">
            <strong class="me-auto"><i class="bi bi-bell-fill"></i> &nbsp; <span class="toast-title"></span></strong>
            <small>&nbsp;</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">
        <strong>User Added Successfully!</strong>
        </div>
    </div>
</div>

<!-- //////////////////////////////////////////     USER TYPE TOASTS      /////////////////////////////////////////////// -->

<div class="toast-container top-0 end-0 p-3">
    <div  id="edit_user_type_successToast"  class="toast position-fixed top-0 end-0 my-4 mx-3 text-white bg-success border-0 " role="alert" aria-live="assertive" aria-atomic="true"
        data-bs-delay="1000">
        <div class="toast-header">
            <strong class="me-auto"><i class="bi bi-bell-fill"></i> &nbsp; <span class="toast-title"></span></strong>
            <small>&nbsp;</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">
        <strong>User Type Updated Successfully!</strong>
        </div>
    </div>
</div>

<div class="toast-container top-0 end-0 p-3">
    <div  id="delete_user_type_successToast"  class="toast position-fixed top-0 end-0 my-4 mx-3 text-black bg-danger border-0 " role="alert" aria-live="assertive" aria-atomic="true"
        data-bs-delay="1000">
        <div class="toast-header">
            <strong class="me-auto"><i class="bi bi-bell-fill"></i> &nbsp; <span class="toast-title"></span></strong>
            <small>&nbsp;</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">
        <strong>User Type Deleted Successfully!</strong>
        </div>
    </div>
</div>


<div class="toast-container top-0 end-0 p-3">
    <div  id="add_user_type_successToast"  class="toast position-fixed top-0 end-0 my-4 mx-3 text-white bg-success border-0 " role="alert" aria-live="assertive" aria-atomic="true"
        data-bs-delay="1000">
        <div class="toast-header">
            <strong class="me-auto"><i class="bi bi-bell-fill"></i> &nbsp; <span class="toast-title"></span></strong>
            <small>&nbsp;</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">
        <strong>User Type Added Successfully!</strong>
        </div>
    </div>
</div>


<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="deleteToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- ////////////////////////////////////////////////   REPORT TOASTS   ////////////////////////////////////////////////// -->
 <div class="toast-container top-0 end-0 p-3">
    <div  id="delete_report_successToast"  class="toast position-fixed top-0 end-0 my-4 mx-3 text-black bg-danger border-0 " role="alert" aria-live="assertive" aria-atomic="true"
        data-bs-delay="1000">
        <div class="toast-header">
            <strong class="me-auto"><i class="bi bi-bell-fill"></i> &nbsp; <span class="toast-title"></span></strong>
            <small>&nbsp;</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">
        <strong>Report Log Deleted Successfully!</strong>
        </div>
    </div>
</div>