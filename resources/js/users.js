import { Modal } from 'bootstrap';
import Triggers from './common/triggers.js';



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
        url:URL + 'delete-user/' + userIdToDelete,
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
        $.get(URL+"get-user/" + userId, function(data) {
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
        component.createDropdown(URL+'getlocation', '#reg_location', data.location_id, '#registerUserModal');
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
    component.createDropdown(URL+'getlocation', '#reg_location', null, '#registerUserModal');
    
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
    };

    // Validation
    if (!formData.emp_code || !formData.user_type) {
        Triggers.showToast('Please fill in required fields.', 1);
        return;
    }

    $btn.prop('disabled', true).text('Processing...');

    if (id === undefined) {
        // --- ADD USER LOGIC ---
        $.ajax({
            url: URL+"addusers",
            type: 'POST',
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
            error: function(xhr) {
                $btn.prop('disabled', false).text('Register User');
                Triggers.showToast(xhr.responseJSON?.message ?? 'Registration failed.', 1);
            }
        });
    } else {
        // --- UPDATE USER LOGIC ---
        formData._method = 'PUT'; // Laravel Method Spoofing
        $.ajax({
            url: URL+"update-user/" + id,
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
            }
        });
    }
});