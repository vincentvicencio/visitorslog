import { Modal } from 'bootstrap';
import container from './common/container';
import datahandling from './common/datahandling';
import Triggers from './common/triggers';
import settable from './common/settable';
import Component from './common/component';
import $ from 'jquery';


const URL = '/registerUser/';
$(document).ready(function() {

    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


    // Handle Dropdown placement without breaking the click event
$(document).on('shown.bs.dropdown', '.dropdown', function () {
    const $toggle = $(this).find('.dropdown-toggle');
    const $menu = $(this).find('.dropdown-menu');

    // Store the original parent so we can put it back later
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
    const $menu = $('body > .dropdown-menu'); // Find the menu we moved to body
    const $parent = $menu.data('parent');
    
    if ($parent) {
        $parent.append($menu); // Put it back where it belongs
        $menu.css({
            'display': '',
            'position': '',
            'top': '',
            'left': ''
        }).removeClass('show');
    }
});

// Initialize the notification modal instance
let notificationModal;
try {
    const notificationModalEl = document.getElementById('notificationContainer');
    if (!notificationModalEl) throw new Error('Notification modal element not found');
    notificationModal = new Modal(notificationModalEl);
} catch (error) {
    console.error('Notification modal initialization failed:', error);
}

let userIdToDelete = null;

// 1. When the delete button in the table is clicked
$(document).on('click', '.delete-user', function() {
    try {
        userIdToDelete = $(this).data('id'); // Store the ID
        
        if (!userIdToDelete) {
            console.warn('Delete button clicked without ID');
            Triggers.showToast('No user ID found.', 1);
            return;
        }
        
        // Set the modal content dynamically
        $('#notification-title').text('Confirm User Deletion');
        $('#notification-message').text('Are you sure you want to delete this user? This action cannot be undone.');
        
        // Ensure the "Yes" button is reset
        $('#btn_ok').prop('disabled', false).text('Yes');

        // Show the modal using the bootstrap instance
        notificationModal?.show();
    } catch (error) {
        console.error('Delete user button error:', error);
        Triggers.showToast('An error occurred.', 1);
    }
});

// 2. When the "Yes" button inside the notification modal is clicked
$('#btn_ok').on('click', function() {
    if (!userIdToDelete) return;

    const btn = $(this);
    btn.prop('disabled', true).text('Processing...');

    $.ajax({
        url:URL + 'delete-user/' + userIdToDelete,
        type: "POST",
        timeout: 10000,
        data: {
            _token: window.Laravel.csrfToken
        },
        success: function(response) {
            // Hide the confirmation modal
            notificationModal?.hide();

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
        error: function(xhr, status, error) {
            if (status === 'timeout') {
                Triggers.showToast('Request timed out. Please try again.', 1);
            } else if (xhr.status === 0) {
                Triggers.showToast('Network error. Check your connection.', 1);
            } else {
                const message = xhr.responseJSON?.message || "Failed to delete user.";
                Triggers.showToast(message, 1);
            }
            console.error('Delete user AJAX Error:', { xhr, status, error });
            btn.prop('disabled', false).text('Yes');
            notificationModal?.hide();
        }
    });
});
$(document).on('click', '#register_btn', function () {
        try {
            openUserModalBlank();
        } catch (error) {
            console.error('Register button error:', error);
            Triggers.showToast('An error occurred while opening the form.', 1);
        }
    });

    // Click Edit Button
    $(document).on('click', '.edit-user', function () {
        try {
            const userId = $(this).data('id');
            if (!userId) {
                console.warn('Edit button clicked without ID');
                return;
            }

            // Fetch user details before opening modal
            $.ajax({
                url: URL+"get-user/" + userId,
                type: 'GET',
                timeout: 10000,
                success: function(data) {
                    openUserModal(data);
                },
                error: function(xhr, status, error) {
                    if (status === 'timeout') {
                        Triggers.showToast('Request timed out. Please try again.', 1);
                    } else if (xhr.status === 0) {
                        Triggers.showToast('Network error. Check your connection.', 1);
                    } else {
                        Triggers.showToast('Failed to load user data.', 1);
                    }
                    console.error('Get user AJAX Error:', { xhr, status, error });
                }
            });
        } catch (error) {
            console.error('Edit user button error:', error);
            Triggers.showToast('An error occurred while opening the edit form.', 1);
        }
    });

});

// Initialize Modal
let userModal;
try {
    const userModalEl = document.getElementById('registerUserModal');
    if (!userModalEl) throw new Error('User modal element not found');
    userModal = new bootstrap.Modal(userModalEl);
} catch (error) {
    console.error('User modal initialization failed:', error);
}

export function openUserModal(data) {
    try {
        const idInput = document.getElementById('reg_user_db_id');
        if (!idInput) {
            console.error('Required form elements not found');
            return;
        }
        
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
            component.createDropdown(URL+'getlocation', '#reg_location', data.location_id, '#registerUserModal');
        } else {
            $('#reg_location').val(data.location_id);
        }

        userModal?.show();
    } catch (error) {
        console.error('openUserModal error:', error);
        Triggers.showToast('Failed to open user form.', 1);
    }
}

export function openUserModalBlank() {
    try {
        const idInput = document.getElementById('reg_user_db_id');
        if (!idInput) {
            console.error('Required form elements not found');
            return;
        }
        
        delete idInput.dataset.id; // Clear ID to signify "Add"
        
        // Reset Fields
        $('#reg_emp_code, #reg_password, #reg_user_type, #reg_location').val('');
        
        // UI Updates
        $('#userModalTitle').text('Register User');
        $('#submit_user_btn').text('Register User');
        $('.edit-only-text').hide();

        // Load locations
        if (typeof component !== 'undefined' && component.createDropdown) {
            component.createDropdown(URL+'getlocation', '#reg_location', null, '#registerUserModal');
        }
        
        userModal?.show();
    } catch (error) {
        console.error('openUserModalBlank error:', error);
        Triggers.showToast('Failed to open registration form.', 1);
    }
}

document.getElementById('submit_user_btn').addEventListener('click', function(e) {
    e.preventDefault();
    
    try {
        const idInput = document.getElementById('reg_user_db_id');
        if (!idInput) {
            console.error('Form element not found');
            Triggers.showToast('Form error. Please refresh the page.', 1);
            return;
        }
        
        const id = idInput.dataset.id;
        const $btn = $(this);

        // Prepare Data
        const formData = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            emp_code: $('#reg_emp_code').val(),
            user_type: $('#reg_user_type').val(),
            locations: $('#reg_location').val(),
            password: $('#reg_password').val()
        };

        // Validation with specific messages
        if (!formData.emp_code) {
            Triggers.showToast('Please enter an employee code.', 1);
            return;
        }
        
        if (!formData.user_type) {
            Triggers.showToast('Please select a user type.', 1);
            return;
        }

        $btn.prop('disabled', true).text('Processing...');

    if (id === undefined) {
        // --- ADD USER LOGIC ---
        $.ajax({
            url: URL+"addusers",
            type: 'POST',
            timeout: 10000,
            data: formData,
            success: function(response) {
                const message = response.success || "User registered Successfully!";
                
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

                // setTimeout(() => {
                //     location.reload();
                // }, 1500);

                if ($.fn.DataTable.isDataTable('#usersTable')) {
                $('#usersTable').DataTable().draw(false);

                }
                $btn.prop('disabled', false).text('Register User');
                userModal.hide(); 
            },
            error: function(xhr, status, error) {
                if (status === 'timeout') {
                    Triggers.showToast('Request timed out. Please try again.', 1);
                } else if (xhr.status === 0) {
                    Triggers.showToast('Network error. Check your connection.', 1);
                } else {
                    Triggers.showToast(xhr.responseJSON?.message ?? 'Failed to register user.', 1);
                }
                console.error('Add user AJAX Error:', { xhr, status, error });
                $btn.prop('disabled', false).text('Register User');
            }
        });
    } else {
        // --- UPDATE USER LOGIC ---
        formData._method = 'PUT'; // Laravel Method Spoofing
        $.ajax({
            url: URL+"update-user/" + id,
            type: 'POST',
            timeout: 10000,
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
            error: function(xhr, status, error) {
                if (status === 'timeout') {
                    Triggers.showToast('Request timed out. Please try again.', 1);
                } else if (xhr.status === 0) {
                    Triggers.showToast('Network error. Check your connection.', 1);
                } else {
                    Triggers.showToast(xhr.responseJSON?.message ?? 'Failed to update user.', 1);
                }
                console.error('Update user AJAX Error:', { xhr, status, error });
                $btn.prop('disabled', false).text('Update User');
            }
        });
    }
    } catch (error) {
        console.error('Submit handler error:', error);
        Triggers.showToast('An error occurred while processing your request.', 1);
        const $btn = $(this);
        const idInput = document.getElementById('reg_user_db_id');
        const id = idInput?.dataset?.id;
        $btn.prop('disabled', false).text(id === undefined ? 'Register User' : 'Update User');
    }
});

// Global error handler for uncaught errors
window.addEventListener('error', (event) => {
    console.error('Global error in users.js:', event.error);
});

window.addEventListener('unhandledrejection', (event) => {
    console.error('Unhandled promise rejection in users.js:', event.reason);
});

class UsersTable {
    constructor() {
        this.defaultFields  = []
        // first parameter of your route
        this.url            = "/registerUser/"
        // id name of your table listing in user
        this.table          = "#usersTable"
        // module
        this.module         = "registerUser"
        // form id
        this.form           = "#"
        // offCanvas
        this.modal          = "#"
        // add user form id
        this.formid         = "#"  
    }

    async onLoadPage(){
        this.list();
    }
    async list() {
        const self = this;

        const tableHeader = [
            { id: "user_name",       label: "Username" },
            { id: "user_type",       label: "Role" },
            { id: "created_by",       label: "Created By" },
            { id: "updated_by",      label: "Updated By" },
            { id: "created_at",   label: "Created Date" },
            { id: "updated_at",   label: "Updated Date" },
            { id: "action",         label: "Action" },
        ];

        const columns = tableHeader.map(col => ({
            data: col.id, 
            title: col.label,
        }));

        const columnDefs = [
            { targets: [0, 1, 2, 3], orderable: false }
        ]; 

        settable.createTableAjax(
            self.table,
            columns,
            self.url,
            columnDefs,
            10,          // ✅ pagination
            {},          // ✅ data
            false
        );

        $(self.table).on('init.dt', function () {

            console.log('✅ DATATABLE INITIALIZED');

            const tableApi = $(self.table).DataTable();

            // 🔥 FORCE DRAW
            tableApi.draw();

            // =========================================
            // CUSTOM SEARCH
            // =========================================
            $('#typeSearch')
                .off('keyup')
                .on('keyup', function () {
                    tableApi.search(this.value).draw();
                });

            // =========================================
            // ENTRIES PER PAGE
            // =================================
            $('#entriesPerPage')
                .off('change')
                .on('change', function () {
                    tableApi.page.len(this.value).draw();
                });
        });
    }


}
const users = new UsersTable();
users.onLoadPage();