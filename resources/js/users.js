import { Modal } from 'bootstrap';
import container from './common/container';
import datahandling from './common/datahandling';
import Triggers from './common/triggers';
import settable from './common/settable';
import component from './common/component';

const URL = '/registerUser/';

    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

    // Allow Bootstrap dropdown menus to render without clipping inside responsive tables.
    const $usersTableWrapper = $('#usersTable').closest(
        '.table-responsive, .table-responsive-sm, .table-responsive-md, .table-responsive-lg'
    );
    if ($usersTableWrapper.length) {
        $usersTableWrapper.css('overflow', 'visible');
    }

    // Ensure dropdown toggles work even when rows are injected by DataTables.
    $(document).on('click', '.dropdown-toggle', function (event) {
        event.preventDefault();
        const dropdown = Dropdown.getOrCreateInstance(this);
        dropdown.toggle();
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

            if ($.fn.DataTable.isDataTable('#usersTable')) {
                $('#usersTable').DataTable().draw(false);

                }
                // $btn.prop('disabled', false).text('Yes');
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

// Initialize Modal
let userModal;
try {
    const userModalEl = document.getElementById('registerUserModal');
    if (!userModalEl) throw new Error('User modal element not found');
    userModal = new bootstrap.Modal(userModalEl);
    
    // Clean up Select2 when modal is hidden
    userModalEl.addEventListener('hidden.bs.modal', function () {
        const locationSelect = $('#reg_location');
        
        if (locationSelect.hasClass('select2-hidden-accessible')) {
            locationSelect.select2('destroy');
        }
        
        locationSelect.removeAttr('multiple');
        $('#reg_emp_code').val('');
        
        // Reset visibility
        $('#reg_fields_container').hide();
        $('#password_container').show();
        $('#emp_code_container').show();
        $('#employee_name_container').hide();
        $('#reg_first_name, #reg_last_name').val('').prop('readonly', true);
    });
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
        
        // Destroy Select2 instances if they exist
        if ($('#reg_location').hasClass('select2-hidden-accessible')) {
            $('#reg_location').select2('destroy');
        }
        
        // Set Data
        idInput.dataset.id = data.id;
        
        // Set the employee code
        $('#reg_emp_code').val(data.emp_code);
        
        // Set first and last name
        $('#reg_first_name').val(data.first_name || '');
        $('#reg_last_name').val(data.last_name || '');
        
        $('#reg_user_type').val(data.role_id); 
        $('#reg_password').val(''); // Clear password for security/edit
        
        // UI Updates
        $('#userModalTitle').text('Edit User');
        $('#submit_user_btn').text('Update User');
        $('.edit-only-text').show();

        // Always show fields when editing
        $('#reg_fields_container').show();

        // Load locations via component helper
        if (typeof component !== 'undefined' && component.createDropdown) {
            component.createDropdown(URL+'getlocation', '#reg_location', data.location_id, '#registerUserModal');
        } else {
            // If location_id is an array, set multiple values
            if (Array.isArray(data.location_id)) {
                $('#reg_location').val(data.location_id);
            } else {
                $('#reg_location').val(data.location_id);
            }
        }
        
        // Trigger role change to setup multi-select if needed
        $('#reg_user_type').trigger('change');
        
        // Handle UI based on role
        const roleName = data.role_name ? data.role_name.toLowerCase() : '';
        
        if (roleName === 'guard') {
            // Guard: show password, editable names, no emp_code
            $('#emp_code_container').hide();
            $('#password_container').show();
            $('#employee_name_container').show();
            $('#reg_first_name, #reg_last_name').prop('readonly', false);
        } else if (roleName === 'admin' || roleName === 'receptionist') {
            // Admin/Receptionist: show emp_code, hide password, show names readonly
            $('#emp_code_container').show();
            $('#password_container').hide();
            if (data.first_name || data.last_name) {
                $('#employee_name_container').show();
                $('#reg_first_name, #reg_last_name').prop('readonly', true);
            }
        } else {
            // Other roles: show emp_code & password
            $('#emp_code_container').show();
            $('#password_container').show();
            if (data.first_name || data.last_name) {
                $('#employee_name_container').show();
            }
        }
        
        // After loading, set the selected values for Select2 if it's multi-location role
        setTimeout(() => {
            if ($('#reg_location').hasClass('select2-hidden-accessible')) {
                $('#reg_location').val(data.location_id).trigger('change');
            }
        }, 500);

        // Ensure single-location roles keep their location selected
        setTimeout(() => {
            if (roleName !== 'admin') {
                const singleLocation = Array.isArray(data.location_id)
                    ? data.location_id[0]
                    : data.location_id;

                if (singleLocation) {
                    $('#reg_location').val(singleLocation).trigger('change');
                }
            }
        }, 500);

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
        
        // Destroy Select2 instances if they exist
        if ($('#reg_location').hasClass('select2-hidden-accessible')) {
            $('#reg_location').select2('destroy');
        }
        
        // Reset Fields
        $('#reg_emp_code').val('');
        $('#reg_password, #reg_user_type, #reg_location').val('');
        $('#reg_first_name, #reg_last_name').val('');
        
        // Reset visibility
        $('#reg_fields_container').hide();
        $('#password_container').show();
        $('#emp_code_container').show();
        $('#employee_name_container').hide();
        $('#reg_location').removeAttr('multiple');
        
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

// Handle role-based multi-location selection with Select2 tags
$(document).on('change', '#reg_user_type', function() {
    const selectedRoleId = $(this).val();
    const selectedRoleText = $(this).find('option:selected').text().trim();
    const locationSelect = $('#reg_location');
    const passwordContainer = $('#password_container');
    const nameContainer = $('#employee_name_container');
    const empCodeContainer = $('#emp_code_container');
    const fieldsContainer = $('#reg_fields_container');
    const isEditing = Boolean(document.getElementById('reg_user_db_id')?.dataset?.id);
    
    // Check if role is Admin (case-insensitive)
    const roleLower = selectedRoleText.toLowerCase();
    const isMultiLocationRole = roleLower === 'admin';
    const isReceptionist = roleLower === 'receptionist';
    const isGuard = selectedRoleText.toLowerCase() === 'guard';
    
    // Destroy existing Select2 instances if they exist
    if (locationSelect.hasClass('select2-hidden-accessible')) {
        locationSelect.select2('destroy');
    }
    
    if (!selectedRoleId) {
        // No role selected: hide all fields
        fieldsContainer.hide();
        passwordContainer.show();
        empCodeContainer.show();
        nameContainer.hide();
        $('#reg_password, #reg_emp_code, #reg_first_name, #reg_last_name').val('');
        locationSelect.val(null).removeAttr('multiple');
        if (locationSelect.find('option[value=""]').length === 0) {
            locationSelect.prepend('<option value="">Select Location</option>');
        }
        return;
    }

    fieldsContainer.show();

    if (isMultiLocationRole) {
        // Admin: Hide password, show emp code with search, hide names until search
        passwordContainer.hide();
        $('#reg_password').val('').removeAttr('required');
        
        empCodeContainer.show();
        nameContainer.hide();
        $('#reg_first_name, #reg_last_name').prop('readonly', true);
        
        // Remove the empty placeholder option for multi-select location
        locationSelect.find('option[value=""]').remove();
        
        // Clear any existing selection before enabling multi-select
        locationSelect.val(null);
        
        // Enable multiple selection with Select2 tags UI
        locationSelect.attr('multiple', 'multiple');
        
        // Initialize Select2 with tag-based UI
        locationSelect.select2({
            placeholder: 'Select locations...',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#registerUserModal'),
            closeOnSelect: false // Keep dropdown open for multiple selections
        });
        
        // Ensure no options are selected after init
        locationSelect.val(null).trigger('change.select2');
    } else if (isReceptionist) {
        // Receptionist: Single location, hide password, show emp code with search
        passwordContainer.hide();
        $('#reg_password').val('').removeAttr('required');

        empCodeContainer.show();
        nameContainer.hide();
        $('#reg_first_name, #reg_last_name').prop('readonly', true);

        // Disable multiple selection for location
        locationSelect.removeAttr('multiple');

        // Add back the placeholder option if it doesn't exist
        if (locationSelect.find('option[value=""]').length === 0) {
            locationSelect.prepend('<option value="">Select Location</option>');
        }

        // Keep only the first selected value if switching from multi to single
        const currentVal = locationSelect.val();
        if (Array.isArray(currentVal) && currentVal.length > 0) {
            locationSelect.val(currentVal[0]);
        }
    } else if (isGuard) {
        // Guard: Show password, show editable names, hide emp code
        passwordContainer.show();
        $('#reg_password').attr('required', 'required');
        
        empCodeContainer.hide();
        $('#reg_emp_code').val('');
        
        nameContainer.show();
        $('#reg_first_name, #reg_last_name').prop('readonly', false);
        if (!isEditing) {
            $('#reg_first_name, #reg_last_name').val('');
        }
        
        // Disable multiple selection for location
        locationSelect.removeAttr('multiple');
        
        // Add back the placeholder option if it doesn't exist
        if (locationSelect.find('option[value=""]').length === 0) {
            locationSelect.prepend('<option value="">Select Location</option>');
        }
        
        // Keep only the first selected value if switching from multi to single
        const currentVal = locationSelect.val();
        if (Array.isArray(currentVal) && currentVal.length > 0) {
            locationSelect.val(currentVal[0]);
        }
    } else {
        // Other roles: Show password, show emp code, hide names
        passwordContainer.show();
        $('#reg_password').attr('required', 'required');
        
        empCodeContainer.show();
        nameContainer.hide();
        $('#reg_first_name, #reg_last_name').val('');
        
        // Disable multiple selection for location
        locationSelect.removeAttr('multiple');
        
        // Add back the placeholder option if it doesn't exist
        if (locationSelect.find('option[value=""]').length === 0) {
            locationSelect.prepend('<option value="">Select Location</option>');
        }
        
        // Keep only the first selected value if switching from multi to single
        const currentVal = locationSelect.val();
        if (Array.isArray(currentVal) && currentVal.length > 0) {
            locationSelect.val(currentVal[0]);
        }
    }
});

// Handle employee code search button click
$(document).on('click', '#search_emp_btn', function() {
    const empCode = $('#reg_emp_code').val().trim();
    
    if (!empCode) {
        Triggers.showToast('Please enter an employee code.', 1);
        return;
    }
    
    const $btn = $(this);
    $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');
    
    $.ajax({
        url: URL + 'search-employees',
        type: 'GET',
        data: { q: empCode },
        timeout: 10000,
        success: function(response) {
            const results = response.results || [];
            const exactMatch = results.find(emp => emp.id.toLowerCase() === empCode.toLowerCase());
            
            if (exactMatch) {
                $('#reg_first_name').val(exactMatch.first_name || '');
                $('#reg_last_name').val(exactMatch.last_name || '');
                $('#employee_name_container').show();
                Triggers.showToast('Employee found!', 0);
            } else if (results.length > 0) {
                // Use first result if no exact match
                $('#reg_first_name').val(results[0].first_name || '');
                $('#reg_last_name').val(results[0].last_name || '');
                $('#reg_emp_code').val(results[0].id);
                $('#employee_name_container').show();
                Triggers.showToast('Employee found!', 0);
            } else {
                $('#reg_first_name').val('');
                $('#reg_last_name').val('');
                $('#employee_name_container').hide();
                Triggers.showToast('Employee code not found.', 1);
            }
            $btn.prop('disabled', false).html('<i class="bi bi-search"></i>');
        },
        error: function(xhr, status, error) {
            Triggers.showToast('Failed to search employee.', 1);
            $btn.prop('disabled', false).html('<i class="bi bi-search"></i>');
        }
    });
});

// Allow pressing Enter in employee code field to trigger search
$(document).on('keypress', '#reg_emp_code', function(e) {
    if (e.which === 13) {
        e.preventDefault();
        $('#search_emp_btn').click();
    }
});

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

        // Get location value - can be single or array
        const locationValue = $('#reg_location').val();
        let locationsData;
        
        // If it's an array, convert to JSON, otherwise use as is
        if (Array.isArray(locationValue)) {
            locationsData = JSON.stringify(locationValue);
        } else {
            locationsData = locationValue;
        }

        // Prepare Data
        const selectedRoleText = $('#reg_user_type option:selected').text().trim().toLowerCase();
        const isGuard = selectedRoleText === 'guard';
        
        const formData = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            emp_code: $('#reg_emp_code').val(),
            user_type: $('#reg_user_type').val(),
            locations: locationsData,
            password: $('#reg_password').val(),
            first_name: $('#reg_first_name').val(),
            last_name: $('#reg_last_name').val()
        };

        // Validation with specific messages based on role
        if (!formData.user_type) {
            Triggers.showToast('Please select a user type.', 1);
            return;
        }

        const hasLocation = Array.isArray(locationValue)
            ? locationValue.length > 0
            : !!locationValue;

        if (!hasLocation) {
            Triggers.showToast('Please select at least one location.', 1);
            return;
        }
        
        if (isGuard) {
            // Guard validation: require first_name and last_name
            if (!formData.first_name) {
                Triggers.showToast('Please enter a first name.', 1);
                return;
            }
            if (!formData.last_name) {
                Triggers.showToast('Please enter a last name.', 1);
                return;
            }
        } else {
            // Other roles: require emp_code
            if (!formData.emp_code) {
                Triggers.showToast('Please enter an employee code.', 1);
                return;
            }
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
        this.url            = "/registerUser/"
        this.table          = "#usersTable"
        this.module         = "users"
        this.form           = "#register_user_form"
        this.modal          = "#registerUserModal"
        this.userModal      = null
        this.initModal()
    }

    initModal() {
        try {
            const userModalEl = document.getElementById('registerUserModal');
            if (!userModalEl) throw new Error('User modal element not found');
            this.userModal = new Modal(userModalEl);

            // Clean up Select2 when modal is hidden
            userModalEl.addEventListener('hidden.bs.modal', function () {
                const locationSelect = $('#reg_location');

                if (locationSelect.hasClass('select2-hidden-accessible')) {
                    locationSelect.select2('destroy');
                }

                locationSelect.removeAttr('multiple');
                $('#reg_emp_code').val('');

                // Reset visibility
                $('#reg_fields_container').hide();
                $('#password_container').show();
                $('#emp_code_container').show();
                $('#employee_name_container').addClass('d-none');
                $('#reg_first_name, #reg_last_name').val('').prop('readonly', true);
            });
        } catch (error) {
            console.error('User modal initialization failed:', error);
        }
    }

    async handleEmployeeSearchClick(event) {
        const empCode = $('#reg_emp_code').val().trim();
        // Require minimum length for employee code (e.g., 4 digits)
        if (!empCode) {
            Triggers.showToast('Please enter an employee code.', 'Employee Search', 1);
            return;
        }
        if (empCode.length < 3) { // Change 4 to your required employee code length
            Triggers.showToast('Please enter the full employee code.','Employee Search', 1);
            return;
        }

        const $btn = $(event.currentTarget);
        $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');

        $.ajax({
            url: this.url + 'search-employees',
            type: 'GET',
            data: { q: empCode },
            timeout: 10000,
            success: function (response) {
                const results = response.results || [];
                // Only allow exact match
                const exactMatch = results.find(emp => emp.id.toLowerCase() === empCode.toLowerCase());

                if (exactMatch) {
                    $('#reg_first_name').val(exactMatch.first_name || '');
                    $('#reg_last_name').val(exactMatch.last_name || '');
                    $('#employee_name_container').removeClass('d-none');
                    Triggers.showToast('Employee found!','Employee Search', 0);
                } else {
                    $('#reg_first_name').val('');
                    $('#reg_last_name').val('');
                    $('#employee_name_container').addClass('d-none');
                    Triggers.showToast('Employee code not found.','Employee Search', 1);
                }
                $btn.prop('disabled', false).html('<i class="bi bi-search"></i>');
            },
            error: function () {
                Triggers.showToast('Failed to search employee.','Employee Search', 1);
                $btn.prop('disabled', false).html('<i class="bi bi-search"></i>');
            }
        });
    }

    initializeEmployeeSearchButton() {
        const searchBtn = document.getElementById('search_emp_btn');
        if (!searchBtn) return;
        searchBtn.removeEventListener('click', this.handleEmployeeSearchClick);
        searchBtn.addEventListener('click', this.handleEmployeeSearchClick.bind(this));
    }

    async InitializePage(){
        const self = this;
        this.list();
        this.initializeButtons();
        this.initializeEmployeeSearchButton();
        component.createDropdown(self.url + 'get-user-type', '#reg_user_type',  null, self.modal);
        this.location_dropdown();
        this.handleRoleChange();
    }

    async handleRoleChange() {
        $(document).off('change', '#reg_user_type').on('change', '#reg_user_type', async function () {
            const selectedRoleId = $(this).val();
            const selectedRoleNum = Number(selectedRoleId);
            const locationSelect = $('#reg_location');
            const passwordContainer = $('#password_container');
            const nameContainer = $('#employee_name_container');
            const empCodeContainer = $('#emp_code_container');
            const fieldsContainer = $('#reg_fields_container');
            const isEditing = Boolean($('#reg_user_db_id').val());

            // Use role IDs: 1 = admin, 2 = receptionist, 3 = guard
            const isMultiLocationRole = selectedRoleNum === 1;
            const isReceptionist = selectedRoleNum === 2;
            const isGuard = selectedRoleNum === 3;

            if (locationSelect.hasClass('select2-hidden-accessible')) {
                locationSelect.select2('destroy');
            }

            if (!selectedRoleId) {
                // No role selected: hide all fields
                fieldsContainer.hide();
                passwordContainer.show();
                empCodeContainer.show();
                nameContainer.addClass('d-none');
                $('#reg_password, #reg_emp_code, #reg_first_name, #reg_last_name').val('');
                locationSelect.val(null).removeAttr('multiple');
                if (locationSelect.find('option[value=""]').length === 0) {
                    locationSelect.prepend('<option value="">Select Location</option>');
                }
                return;
            }

            fieldsContainer.show();

            if (isMultiLocationRole) {
                // Admin: Hide password, show emp code with search
                passwordContainer.hide();
                $('#reg_password').val('').removeAttr('required');

                empCodeContainer.show();
                // If editing, show name fields as readonly and filled
                if (isEditing) {
                    nameContainer.removeClass('d-none');
                    $('#reg_first_name, #reg_last_name').prop('readonly', true);
                } else {
                    nameContainer.addClass('d-none');
                    $('#reg_first_name, #reg_last_name').val('').prop('readonly', true);
                }

                // Remove the empty placeholder option for multi-select location
                // locationSelect.find('option[value=""]').remove();

                // Clear any existing selection before enabling multi-select
                locationSelect.val(null);

                // Enable multiple selection with Select2 tags UI
                // locationSelect.attr('multiple', 'multiple');
                
                locationSelect.removeAttr('multiple');

                if (locationSelect.find('option[value=""]').length === 0) {
                    locationSelect.prepend('<option value="">Select Location</option>');
                }

                // Initialize Select2 with tag-based UI
                // locationSelect.select2({
                //     placeholder: 'Select locations...',
                //     allowClear: true,
                //     width: '100%',
                //     dropdownParent: $('#registerUserModal'),
                //     closeOnSelect: false
                // });

                // Ensure no options are selected after init
                locationSelect.val(null).trigger('change.select2');
            } else if (isReceptionist) {
                // Receptionist: Single location, hide password, show emp code with search
                passwordContainer.hide();
                $('#reg_password').val('').removeAttr('required');

                empCodeContainer.show();
                // If editing, show name fields as readonly and filled
                if (isEditing) {
                    nameContainer.removeClass('d-none');
                    $('#reg_first_name, #reg_last_name').prop('readonly', true);
                } else {
                    nameContainer.addClass('d-none');
                    $('#reg_first_name, #reg_last_name').val('').prop('readonly', true);
                }

                // Disable multiple selection for location
                locationSelect.removeAttr('multiple');

                // Add back the placeholder option if it doesn't exist
                if (locationSelect.find('option[value=""]').length === 0) {
                    locationSelect.prepend('<option value="">Select Location</option>');
                }

                // Keep only the first selected value if switching from multi to single
                const currentVal = locationSelect.val();
                if (Array.isArray(currentVal) && currentVal.length > 0) {
                    locationSelect.val(currentVal[0]);
                }
            } else if (isGuard) {
                // Guard: Show password, show editable names, hide emp code
                passwordContainer.show();
                $('#reg_password').attr('required', 'required');

                empCodeContainer.hide();
                $('#reg_emp_code').val('');

                nameContainer.removeClass('d-none');
                $('#reg_first_name, #reg_last_name').prop('readonly', false);
                if (!isEditing) {
                    $('#reg_first_name, #reg_last_name').val('');
                }

                // Disable multiple selection for location
                locationSelect.removeAttr('multiple');

                // Add back the placeholder option if it doesn't exist
                if (locationSelect.find('option[value=""]').length === 0) {
                    locationSelect.prepend('<option value="">Select Location</option>');
                }

                // Keep only the first selected value if switching from multi to single
                const currentVal = locationSelect.val();
                if (Array.isArray(currentVal) && currentVal.length > 0) {
                    locationSelect.val(currentVal[0]);
                }
            } else {
                // Other roles: Show password, show emp code, hide names
                passwordContainer.show();
                $('#reg_password').attr('required', 'required');

                empCodeContainer.show();
                nameContainer.addClass('d-none');
                $('#reg_first_name, #reg_last_name').val('');

                // Disable multiple selection for location
                locationSelect.removeAttr('multiple');

                // Add back the placeholder option if it doesn't exist
                if (locationSelect.find('option[value=""]').length === 0) {
                    locationSelect.prepend('<option value="">Select Location</option>');
                }

                // Keep only the first selected value if switching from multi to single
                const currentVal = locationSelect.val();
                if (Array.isArray(currentVal) && currentVal.length > 0) {
                    locationSelect.val(currentVal[0]);
                }
            }
        });
    }

    async list() {
        const self = this;

        const tableHeader = [
            { id: "user_name",       label: "Username"     },
            { id: "user_type",       label: "Role"         },
            { id: "created_by",      label: "Created By"   },
            { id: "updated_by",      label: "Updated By"   },
            { id: "created_at",      label: "Created Date" },
            { id: "updated_at",      label: "Updated Date" },
            { id: "action",          label: "Action"       },
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
            self.module,
            10,
            {},
            false
        );

        $(self.table).on('init.dt', function () {

            const tableApi = $(self.table).DataTable();

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
            // =========================================
            $('#entriesPerPage')
                .off('change')
                .on('change', function () {
                    tableApi.page.len(this.value).draw();
                });
        });
    }

    async initializeButtons(){
        const self = this;
        // Add new user
        $('#reg_user').off('click').on('click', async function (e) {
            e.preventDefault();
            datahandling.clearForm(self.form);
            $('#reg_user_db_id').val('');
            container.showModal(self.modal);
        });

        // Save
        $('#submit_user_btn').off('click').on('click', async function(e) {
            e.preventDefault();
            const formid    = self.form;
            const formdata  = new FormData($(formid)[0]);
            const idInput = document.getElementById('reg_user_db_id');
            const recordId = idInput?.value || idInput?.dataset?.id || '';
            if (recordId) {
                formdata.set('record_id', recordId);
            }
            await Triggers.removeErrorOnInput(formid);
            await datahandling.saveForm(self.url + 'save', self.table, self.form, formdata);
        });

        $(document).off('click', '.btn-edit').on('click', '.btn-edit', async function(e) {
            e.preventDefault();
            const userId = $(this).attr('data-id');
            if (userId) {
                await self.onLoadForm(userId);
            }
        });
    }
        
    async onLoadForm(record_id) {
        const self = this;
        try {
            const url = `${self.url}get-user/${record_id}`;
            const response = await datahandling.processData(url, 'GET');
            if (response && response.id) {
                // Fill fields
                $('#reg_user_db_id').val(response.id);
                $('#reg_emp_code').val(response.emp_code || '');
                $('#reg_user_type').val(response.role_id || '');
                
                // Trigger change event on user type to update UI (after all values set)
                $('#reg_user_type').trigger('change');

                // Fill usertype
                $('#reg_user_type').trigger('change');

                // Fill location
                await self.location_dropdown(response.location_id);
                if (Array.isArray(response.location_id)) {
                    $('#reg_location').val(response.location_id).trigger('change');
                } else if (response.location_id) {
                    $('#reg_location').val([response.location_id]).trigger('change');
                } else {
                    $('#reg_location').val('').trigger('change');
                }

                

                // Always show and fill first/last name fields after role change
                $('#employee_name_container').removeClass('d-none');
                $('#reg_first_name').val(response.first_name || '');
                $('#reg_last_name').val(response.last_name || '');

                // Fill user type
                const roleName = (response.role_name || '').toLowerCase();
                if (roleName === 'guard') {
                    $('#reg_first_name, #reg_last_name').prop('readonly', false);
                } else {
                    $('#reg_first_name, #reg_last_name').prop('readonly', true);
                }

                // Show modal
                container.showModal(self.modal);
            }
        } catch (error) {
            console.error('Failed to load user data:', error);
            Triggers.showToast('Failed to load user data.','User Data', 1);
        }
    }

    async location_dropdown(selectedValue = null) {
        const self = this;

        // Destroy existing Select2
        if ($('#reg_location').hasClass('select2-hidden-accessible')) {
            $('#reg_location').select2('destroy');
        }

        component.createDropdown(URL + 'getlocation', '#reg_location', selectedValue, '#registerUserModal');
    }
}
const instance = new UsersTable();
instance.InitializePage();

export default instance;