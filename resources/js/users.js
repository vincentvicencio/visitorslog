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

        // Wire custom entries per page to DataTable
        $('#entriesPerPage').off('change').on('change', function() {
            $('#userTable').DataTable().page.len($(this).val()).draw();
        });
    }

    // Expose reload function globally for after CRUD operations
    window.reloadUsersTable = function() {
        initUsersTable();
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
                $('#toastMessage').text(response.success || "User Added Successfully!");
                const toastElement = document.getElementById('SUCCESSTOAST');
                const toast = new bootstrap.Toast(toastElement);
                toast.show();

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
            }
        });
    });
});