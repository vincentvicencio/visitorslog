$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.Laravel.csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    // --- DATATABLE SETUP ---
    initUsersTable();

    function initUsersTable() {
        // Column definitions
        const theads = [
            { data: 'first_name', title: 'Username' },
            { data: 'role', title: 'Role' },
            { data: 'created_by', title: 'Created by' },
            { data: 'updated_by', title: 'Updated by' },
            { data: 'created_at', title: 'Created date' },
            { data: 'id', title: 'Action' }
        ];

        // Column render definitions
        const tbodies = [
            {
                targets: 5, // Action column
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" 
                                type="button" 
                                data-bs-toggle="dropdown" 
                                data-bs-boundary="viewport" 
                                aria-expanded="false">
                                Action
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item edit-user" href="javascript:void(0)" data-id="${data}">
                                        <i class="bi bi-pencil-square me-2"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item text-danger delete-user" data-id="${data}">
                                        <i class="bi bi-trash me-2"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    `;
                }
            }
        ];

        // Use the DataTable 
        settable.createTable(
            '#userTable',   // table selector
            theads,         // columns
            '/users/',      // url (will append 'list')
            tbodies,        // columnDefs
            'users',        // module name for component.initializeButtons
            false,          // enableSearch - disabled
            10              // pagination
        );

        // Wire custom search input to DataTable
        $('#tableSearch').off('keyup').on('keyup', function() {
            $('#userTable').DataTable().search($(this).val()).draw();
        });

<<<<<<< HEAD
        // Wire custom entries per page to DataTable
        $('#entriesPerPage').off('change').on('change', function() {
            $('#userTable').DataTable().page.len($(this).val()).draw();
        });
    }

    // Expose reload function globally for after CRUD operations
    window.reloadUsersTable = function() {
        initUsersTable();
=======
        currentPage = 1; // Reset to first page on new search
        applyPagination(); 
    });

    // --- 4. PAGINATION CORE FUNCTION ---
    function applyPagination() {
        const limit = parseInt($('#entriesPerPage').val()) || 10;
        const $allRows = $("#employeeTableBody tr");
        const $rowsToPaginate = $allRows.filter('.search-match');

        const totalRows = $rowsToPaginate.length;
        const totalPages = Math.ceil(totalRows / limit) || 1;

        // Boundary checks
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        // Hide all rows, then show only the current slice
        $allRows.hide();
        const start = (currentPage - 1) * limit;
        const end = start + limit;
        $rowsToPaginate.slice(start, end).show();

        // Update the custom pagination UI text
        $('.number-holder-pagination').text(`Page ${currentPage} of ${totalPages}`);

        // Visual feedback for arrows (opacity and cursor)
        updateArrowStyles(currentPage, totalPages);
    }



    // --- 5. EVENT LISTENERS ---

    // Entries Per Page Change
    $('#entriesPerPage').on('change', function() {
        currentPage = 1;
        applyPagination();
    });



    $(document).on('click', '.pagination-prev', function() {
        if (currentPage > 1) {
            currentPage--;
            applyPagination();
        }
    });

    $(document).on('click', '.pagination-next', function() {
        const limit = parseInt($('#entriesPerPage').val());
        const totalPages = Math.ceil($("#employeeTableBody tr.search-match").length / limit);
        if (currentPage < totalPages) {
            currentPage++;
            applyPagination();
        }
    });

    $(document).on('click', '.pagination-last', function() {
        const limit = parseInt($('#entriesPerPage').val());
        const totalPages = Math.ceil($("#employeeTableBody tr.search-match").length / limit);
        if (currentPage < totalPages) {
            currentPage = totalPages;
            applyPagination();
        }
    });

// Initialize the notification modal instance
const notificationModalEl = document.getElementById('notificationContainer');
const notificationModal = new Modal(notificationModalEl);

let userIdToDelete = null;

// 1. When the delete button in the table is clicked
$(document).on('click', '.delete-user', function() {
    userIdToDelete = $(this).data('id'); // Store the ID
    
    // Set the modal content dynamically
    $('#notification-title').text('Confirm User Deletion');
    $('#notification-message').text('Are you sure you want to delete this user? This action cannot be undone.');
    
    // Ensure the "Yes" button is reset
    $('#btn_ok').prop('disabled', false).text('Yes');

    // Show the modal using the bootstrap instance
    notificationModal.show();
});

// 2. When the "Yes" button inside the notification modal is clicked
$('#btn_ok').on('click', function() {
    if (!userIdToDelete) return;

    const btn = $(this);
    btn.prop('disabled', true).text('Processing...');

    $.ajax({
        url:'/delete-user/' + userIdToDelete,
        type: "POST",
        data: {
            _token: window.Laravel.csrfToken
        },
        success: function(response) {
            // Hide the confirmation modal
            notificationModal.hide();

            // Handle the Toast
            $('#DeletetoastMessage').text(response.success || "User Deleted Successfully!");
            const toastElement = document.getElementById('DELETE');
            if (toastElement) {
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
            }

            // Reload the page
            // setTimeout(function() {
            //     location.reload();
            // }, 1500); 

            if ($.fn.DataTable.isDataTable('#usersTable')) {
                $('#usersTable').DataTable().draw(false);

                }
                $btn.prop('disabled', false).text('Yes');
                userModal.hide();
        },
        error: function(xhr) {
            alert("Error deleting user: " + (xhr.responseJSON?.message || "Internal Server Error"));
            btn.prop('disabled', false).text('Yes');
            notificationModal.hide();
        }
    });
});
$(document).on('click', '#register_btn', function () {
        openUserModalBlank();
    });

    // Click Edit Button
    $(document).on('click', '.edit-user', function () {
        const userId = $(this).data('id');
        if (!userId) return;

        // Fetch user details before opening modal
        $.get("/get-user/" + userId, function(data) {
            openUserModal(data);
        });
    });

});

// Initialize Modal
const userModalEl = document.getElementById('registerUserModal');
const userModal = new bootstrap.Modal(userModalEl);

export function openUserModal(data) {
    const idInput = document.getElementById('reg_user_db_id');
    
    // Set Data
    idInput.dataset.id = data.id; 
    $('#reg_emp_code').val(data.emp_code); 
    $('#reg_user_type').val(data.role_id); 
    $('#reg_password').val(''); // Clear password for security/edit
    
    // UI Updates
    $('#userModalTitle').text('Edit User');
    $('#submit_user_btn').text('Update User');
    $('.edit-only-text').show();

    // Load locations via component helper
    if (typeof component !== 'undefined' && component.createDropdown) {
        component.createDropdown('/getlocation', '#reg_location', data.location_id, '#registerUserModal');
    } else {
        $('#reg_location').val(data.location_id);
    }

    userModal.show();
}

export function openUserModalBlank() {
    const idInput = document.getElementById('reg_user_db_id');
    delete idInput.dataset.id; // Clear ID to signify "Add"
    
    // Reset Fields
    $('#reg_emp_code, #reg_password, #reg_user_type, #reg_location').val('');
    
    // UI Updates
    $('#userModalTitle').text('Register User');
    $('#submit_user_btn').text('Register User');
    $('.edit-only-text').hide();

    // Load locations
    component.createDropdown('/getlocation', '#reg_location', null, '#registerUserModal');
    
    userModal.show();
}

document.getElementById('submit_user_btn').addEventListener('click', function(e) {
    e.preventDefault();
    
    const idInput = document.getElementById('reg_user_db_id');
    const id = idInput.dataset.id;
    const $btn = $(this);

    // Prepare Data
    const formData = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        emp_code: $('#reg_emp_code').val(),
        user_type: $('#reg_user_type').val(),
        locations: $('#reg_location').val(),
        password: $('#reg_password').val()
>>>>>>> 2a4ab0cb42c6aaae97481c05e6bcbd767b38e817
    };

    // --- DROPDOWN PLACEMENT FIX (for scrolling tables) ---
    $(document).on('shown.bs.dropdown', '.dropdown', function () {
        const $toggle = $(this).find('.dropdown-toggle');
        const $menu = $(this).find('.dropdown-menu');

        $menu.data('parent', $(this));
        $('body').append($menu);
        
        const offset = $toggle.offset();
        $menu.css({
            'display': 'block',
            'position': 'absolute',
            'visibility': 'visible',
            'opacity': '1',
            'top': offset.top + $toggle.outerHeight(),
            'left': offset.left,
            'z-index': '9999'
        }).addClass('show');
    });

    $(document).on('hide.bs.dropdown', '.dropdown', function () {
        const $menu = $('body > .dropdown-menu');
        const $parent = $menu.data('parent');
        
        if ($parent) {
            $parent.append($menu);
            $menu.css({
                'display': '',
                'position': '',
                'top': '',
                'left': ''
            }).removeClass('show');
        }
    });

    // --- REGISTER USER ---
    $('#register_btn').click(function() { 
        $('#registeruserpopup').fadeIn(); 
        component.createDropdown('/getlocation', '#reg_location', null, '#registeruserpopup');
    });
    
    $('#close_register_user_popup').click(function() { 
        $('#registeruserpopup').fadeOut(); 
    });

    // Handle AJAX Submission
    $('#registered_user_form').on('submit', function(e) {
        e.preventDefault(); 

        $.ajax({
            url: window.Laravel.baseUrl + '/addusers', 
            type: "POST",
            data: {
                _token: window.Laravel.csrfToken,
                emp_code: $('#reg_emp_code').val(),
                password: $('#reg_password').val(),
                user_type: $('#reg_user_type').val(),
                locations: $('#reg_location').val(),
            },
            success: function(response) {
<<<<<<< HEAD
                $('#toastMessage').text(response.success || "User Added Successfully!");
=======
                const message = response.success || "User registered Successfully!";
                
                // 1. Set the Title (Optional but looks better)
                $('.toast-title').text("Success");
                
                // 2. Set the Body Text
                $('#toastMessageforadd').text(message);

                // 3. Show the Toast
>>>>>>> 2a4ab0cb42c6aaae97481c05e6bcbd767b38e817
                const toastElement = document.getElementById('SUCCESSTOAST');
                const toast = new bootstrap.Toast(toastElement);
                toast.show();

<<<<<<< HEAD
                // Clear form fields
                $('#registered_user_form')[0].reset();
                $('#reg_location').val('').trigger('change'); // Reset Select2

                // Reload DataTable instead of page
                setTimeout(function() {
                    $('#registeruserpopup').fadeOut();
                    window.reloadUsersTable();
                }, 1000); 
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert("Validation Error: " + xhr.responseJSON.message);
            }
        });
    });

    // --- DELETE USER LOGIC ---
    let userIdToDelete = null;

    $(document).on('click', '.delete-user', function() {
        userIdToDelete = $(this).data('id');
        $('#deleteConfirmModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        if (!userIdToDelete) return;

        const btn = $(this);
        btn.prop('disabled', true).text('Processing...');

        $.ajax({
            url: window.Laravel.baseUrl + '/delete-user/' + userIdToDelete,
            type: "POST",
            data: {
                _token: window.Laravel.csrfToken
            },
            success: function(response) {
                $('#DeletetoastMessage').text(response.success || "User Deleted Successfully!");
                const toastElement = document.getElementById('DELETE');
                const toast = new bootstrap.Toast(toastElement);
                toast.show();

                setTimeout(function() {
                    $('#deleteConfirmModal').modal('hide');
                    btn.prop('disabled', false).text('Delete');
                    window.reloadUsersTable();
                }, 1500); 
            },
            error: function(xhr) {
                alert("Error deleting user.");
                btn.prop('disabled', false).text('Delete');
                $('#deleteConfirmModal').modal('hide');
            }
        });
    });

    // --- EDIT USER LOGIC ---
    $(document).on('click', '.edit-user', function() {
        var userId = $(this).data('id');
        
        $.get(window.Laravel.baseUrl + "/get-user/" + userId, function(data) {
            $('#edit_user_id').val(data.id);
            $('#edit_user_type').val(data.user_type); 
            $('#edit_emp_code').val(data.user_name);
            $('#editPopupContainer').fadeIn();
        }).fail(function(xhr) {
            console.error("Fetch failed: ", xhr.status);
        });
    });

    $('#closeEditPopup').click(function() { 
        $('#editPopupContainer').hide(); 
    });

    $('#edit_user_form').on('submit', function(e) {
        e.preventDefault();
        var userId = $('#edit_user_id').val(); 

        $.ajax({
            url: window.Laravel.baseUrl + '/update-user/' + userId,
            type: "POST",
            data: $(this).serialize(), 
            success: function(response) {
                $('#editPopupContainer').fadeOut(200);
                $('#toastMessage').text(response.message || "User updated successfully!");

                const toastElement = document.getElementById('SUCCESSTOAST');
                const toast = new bootstrap.Toast(toastElement);
                toast.show();

                setTimeout(function() {
                    window.reloadUsersTable();
                }, 1500);
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    alert("Your session has expired. Please refresh and log in again.");
                } else {
                    alert("Error: " + (xhr.responseJSON ? xhr.responseJSON.message : "Update failed"));
                }
=======
                // setTimeout(() => {
                //     location.reload();
                // }, 1500);

                if ($.fn.DataTable.isDataTable('#usersTable')) {
                $('#usersTable').DataTable().draw(false);

                }
                $btn.prop('disabled', false).text('Register User');
                userModal.hide(); 
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text('Register User');
                Triggers.showToast(xhr.responseJSON?.message ?? 'Registration failed.', 1);
            }
        });
    } else {
        // --- UPDATE USER LOGIC ---
        formData._method = 'PUT'; // Laravel Method Spoofing
        $.ajax({
            url: "/update-user/" + id,
            type: 'POST',
            data: formData,
            success: function(response) {

                const message = response.success || "User Updated Successfully!";
                
                // 1. Set the Title (Optional but looks better)
                $('.toast-title').text("Success");
                
                // 2. Set the Body Text
                $('#toastMessageforadd').text(message);

                // 3. Show the Toast
                const toastElement = document.getElementById('SUCCESSTOAST');
                if (toastElement) {
                    const toast = new bootstrap.Toast(toastElement);
                    toast.show();
                }

                if ($.fn.DataTable.isDataTable('#usersTable')) {
                $('#usersTable').DataTable().draw(false);

                }
                $btn.prop('disabled', false).text('Update User');
                userModal.hide();
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text('Update User');
                Triggers.showToast(xhr.responseJSON?.message ?? 'Update failed.', 1);
>>>>>>> 2a4ab0cb42c6aaae97481c05e6bcbd767b38e817
            }
        });
    });
});