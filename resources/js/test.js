import { Modal, Dropdown } from 'bootstrap';
import container from './common/container';
import datahandling from './common/datahandling';
import Triggers from './common/triggers';
import settable from './common/settable';
import component from './common/component';


const URL = '/registerUser/';

// Initialize Modal
let userModal;
try {
    const userModalEl = document.getElementById('registerUserModal');
    if (!userModalEl) throw new Error('User modal element not found');
    userModal = new Modal(userModalEl);

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

function handleEmployeeSearchClick(event) {
    const empCode = $('#reg_emp_code').val().trim();

    if (!empCode) {
        Triggers.showToast('Please enter an employee code.', 1);
        return;
    }

    const $btn = $(event.currentTarget);
    $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');

    $.ajax({
        url: URL + 'search-employees',
        type: 'GET',
        data: { q: empCode },
        timeout: 10000,
        success: function (response) {
            const results = response.results || [];
            const exactMatch = results.find(emp => emp.id.toLowerCase() === empCode.toLowerCase());

            if (exactMatch) {
                $('#reg_first_name').val(exactMatch.first_name || '');
                $('#reg_last_name').val(exactMatch.last_name || '');
                $('#employee_name_container').removeClass('d-none');
                Triggers.showToast('Employee found!', 0);
            } else if (results.length > 0) {
                $('#reg_first_name').val(results[0].first_name || '');
                $('#reg_last_name').val(results[0].last_name || '');
                $('#reg_emp_code').val(results[0].id);
                $('#employee_name_container').removeClass('d-none');
                Triggers.showToast('Employee found!', 0);
            } else {
                $('#reg_first_name').val('');
                $('#reg_last_name').val('');
                $('#employee_name_container').addClass('d-none');
                Triggers.showToast('Employee code not found.', 1);
            }
            $btn.prop('disabled', false).html('<i class="bi bi-search"></i>');
        },
        error: function () {
            Triggers.showToast('Failed to search employee.', 1);
            $btn.prop('disabled', false).html('<i class="bi bi-search"></i>');
        }
    });
}

function initializeEmployeeSearchButton() {
    const searchBtn = document.getElementById('search_emp_btn');
    if (!searchBtn) return;
    searchBtn.removeEventListener('click', handleEmployeeSearchClick);
    searchBtn.addEventListener('click', handleEmployeeSearchClick);
}

// Handle role-based multi-location selection with Select2 tags
$(document).on('change', '#reg_user_type', function () {
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
        // Admin: Hide password, show emp code with search, hide names until search
        passwordContainer.hide();
        $('#reg_password').val('').removeAttr('required');

        empCodeContainer.show();
        nameContainer.addClass('d-none');
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
            closeOnSelect: false
        });

        // Ensure no options are selected after init
        locationSelect.val(null).trigger('change.select2');
    } else if (isReceptionist) {
        // Receptionist: Single location, hide password, show emp code with search
        passwordContainer.hide();
        $('#reg_password').val('').removeAttr('required');

        empCodeContainer.show();
        nameContainer.addClass('d-none');
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

class UsersTable {

    constructor() {
        this.defaultFields  = []
        // first parameter of your route
        this.url            = "/registerUser/"
        // id name of your table listing in user
        this.table          = "#usersTable"
        // module
        this.module         = "test"
        // form id
        this.form           = "#register_user_form"
        // offCanvas
        this.modal          = "#registerUserModal"
    }

    async InitializePage(){
        const self = this;

        this.list();
        this.initializeButtons();
        initializeEmployeeSearchButton();
        component.createDropdown(self.url + 'get-user-type', '#reg_user_type',  null, self.modal) 
        self.location_dropdown()
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
            self.module,
            10,          //  pagination
            {},          //  data
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
            // =================================
            $('#entriesPerPage')
                .off('change')
                .on('change', function () {
                    tableApi.page.len(this.value).draw();
                });
        });
        
    }

    async initializeButtons(){
            const self = this
            $('#reg_user').off('click').on('click', async function (e) {
                e.preventDefault()
                    datahandling.clearForm(self.form)
                    // openUserModalBlank();
                    container.showModal(self.modal)
            })
            
            $(document).off('click', '#submit_user_btn').on('click', '#submit_user_btn', async function(e) {
                e.preventDefault();
                
                const formid    = self.form;
                const formdata  = new FormData($(formid)[0]);
                
                // Ensure record_id is included (from hidden input or dataset)
                const idInput = document.getElementById('reg_user_db_id');
                const recordId = idInput?.value || idInput?.dataset?.id || '';
                if (recordId) {
                    formdata.set('record_id', recordId);
                }
                
                await Triggers.removeErrorOnInput(formid);
                await datahandling.saveForm(self.url + 'save', self.table, self.form, formdata)
    
            });
    
        }
        
    async onLoadForm(record_id) {
        const self = this;
        try {
            const url = `${self.url}get-user/${record_id}`;
            const response = await datahandling.processData(url, 'GET');
            
            // Re-initialize location dropdown with pre-selected values
            if (response && response.location_id) {
                self.location_dropdown(response.location_id);
            }
        } catch (error) {
            console.error('Failed to load user data:', error);
            Triggers.showToast('Failed to load user data.', 1);
        }
    }

    async location_dropdown(selectedValue = null) {
        const self = this;

        // Destroy existing Select2 instance before re-initializing
        if ($('#reg_location').hasClass('select2-hidden-accessible')) {
            $('#reg_location').select2('destroy');
        }

        component.createDropdown(URL + 'getlocation', '#reg_location', selectedValue, '#registerUserModal');
    }

}
const instance = new UsersTable();
instance.InitializePage();

export default instance;