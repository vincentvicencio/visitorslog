import { Modal } from 'bootstrap';
import container from './common/container';
import datahandling from './common/datahandling';
import Triggers from './common/triggers';
import settable from './common/settable';
import component from './common/component';

const URL = '/registerUser/';

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

    // Hide the edit-only-text when adding a new user
    resetEditOnlyText() {
        $('.edit-only-text').hide();
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
        // Require non-empty employee code
        if (!empCode) {
            Triggers.showToast('Please enter an employee code.', 'Employee Search', 1);
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
                    // Set searched emp code field
                    $('#searched_emp_code').val(empCode);
                    $('#searched_emp_code_container').show();
                    Triggers.showToast('Employee found!','Employee Search', 0);
                } else {
                    $('#reg_first_name').val('');
                    $('#reg_last_name').val('');
                    $('#employee_name_container').addClass('d-none');
                    $('#searched_emp_code').val('');
                    $('#searched_emp_code_container').hide();
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
        this.keylistener();
    }

    async handleRoleChange() {
        $(document).off('change', '#reg_user_type').on('change', '#reg_user_type', async function () {
            const selectedRoleId = $(this).val();
            const selectedRoleNum = Number(selectedRoleId);
            const locationSelect = $('#reg_location');
            const locationContainer = $('#location_container');
            const passwordContainer = $('#password_container');
            const nameContainer = $('#employee_name_container');
            const empCodeContainer = $('#emp_code_container');
            const fieldsContainer = $('#reg_fields_container');
            const isEditing = Boolean($('#reg_user_db_id').val());

            // --- Error clearing logic for role field ---
            const roleInput = document.getElementById('reg_user_type');
            const roleFeedback = document.getElementById('roleFeedback');
            if (roleInput && roleFeedback) {
                if (selectedRoleId && selectedRoleId !== '') {
                    roleInput.classList.remove('is-invalid');
                    roleFeedback.style.display = '';
                    roleFeedback.textContent = '';
                }
            }
            // --- End error clearing logic ---

            // Use role IDs: 1 = admin, 2 = receptionist, 3 = guard
            const isMultiLocationRole = selectedRoleNum === 1;
            const isReceptionist = selectedRoleNum === 2;
            const isGuard = selectedRoleNum === 3;

            // Remove WFH option for Guard and Receptionist
            if (isGuard || isReceptionist) {
                // Remove all options with value or text containing 'WFH' (case-insensitive)
                locationSelect.find('option').filter(function() {
                    return $(this).val().toLowerCase().includes('wfh') || $(this).text().toLowerCase().includes('wfh');
                }).remove();
            }

            if (locationSelect.hasClass('select2-hidden-accessible')) {
                locationSelect.select2('destroy');
            }

            if (!selectedRoleId) {
                // No role selected: hide all fields
                fieldsContainer.hide();
                locationContainer.show();
                passwordContainer.show();
                empCodeContainer.show();
                nameContainer.addClass('d-none');
                $('#reg_password, #reg_emp_code, #reg_first_name, #reg_last_name').val('');
                    // Hide searched employee code field
                    $('#searched_emp_code').val('');
                    $('#searched_emp_code_container').hide();
                locationSelect.val(null).removeAttr('multiple');
                if (locationSelect.find('option[value=""]').length === 0) {
                    locationSelect.prepend('<option value="">Select Location</option>');
                }
                return;
            }

            fieldsContainer.show();

            if (isMultiLocationRole) {
                locationContainer.show();
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
                        // Hide searched employee code field
                        $('#searched_emp_code').val('');
                        $('#searched_emp_code_container').hide();
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
                locationContainer.show();
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
                        // Hide searched employee code field
                        $('#searched_emp_code').val('');
                        $('#searched_emp_code_container').hide();
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
                locationContainer.hide();
                // Guard: Show password, show editable names, hide emp code
                passwordContainer.show();
                $('#reg_password').attr('required', 'required');

                empCodeContainer.hide();
                $('#reg_emp_code').val('');
                    // Hide searched employee code field
                    $('#searched_emp_code').val('');
                    $('#searched_emp_code_container').hide();

                nameContainer.removeClass('d-none');
                $('#reg_first_name, #reg_last_name').prop('readonly', false);
                if (!isEditing) {
                    $('#reg_first_name, #reg_last_name').val('');
                }

                // Guard does not have fixed company location
                locationSelect.val('').trigger('change');

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
                locationContainer.show();
                // Other roles: Show password, show emp code, hide names
                passwordContainer.show();
                $('#reg_password').attr('required', 'required');

                empCodeContainer.show();
                nameContainer.addClass('d-none');
                $('#reg_first_name, #reg_last_name').val('');
                    // Hide searched employee code field
                    $('#searched_emp_code').val('');
                    $('#searched_emp_code_container').hide();

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
                // Clear error on input/change for all validated fields including role
                const clearOnInputFields = [
                    {input: 'reg_user_type', feedback: 'roleFeedback', event: 'change'},
                    {input: 'reg_location', feedback: 'locationFeedback', event: 'change'},
                    {input: 'reg_emp_code', feedback: 'empCodeFeedback', event: 'input'},
                    {input: 'reg_first_name', feedback: 'firstNameFeedback', event: 'input'},
                    {input: 'reg_last_name', feedback: 'lastNameFeedback', event: 'input'},
                    {input: 'reg_password', feedback: 'passwordFeedback', event: 'input'}
                ];
                // Use event delegation for select fields to handle dynamic options
                $(document).on('change', '#reg_user_type', function() {
                    const input = this;
                    const feedback = document.getElementById('roleFeedback');
                    if (input.value && input.value !== '') {
                        input.classList.remove('is-invalid');
                        feedback.style.display = '';
                        feedback.textContent = '';
                    }
                });
                $(document).on('change', '#reg_location', function() {
                    const input = this;
                    const feedback = document.getElementById('locationFeedback');
                    if (input.value && input.value !== '') {
                        input.classList.remove('is-invalid');
                        feedback.style.display = '';
                        feedback.textContent = '';
                    }
                });
                clearOnInputFields.forEach(f => {
                    const input = document.getElementById(f.input);
                    const feedback = document.getElementById(f.feedback);
                    if (input && feedback) {
                        input.addEventListener(f.event, function() {
                            if (input.value.trim()) {
                                input.classList.remove('is-invalid');
                                feedback.style.display = '';
                                feedback.textContent = '';
                            }
                        });
                    }
                });
        const self = this;
        // Add new user
        $('#reg_user').off('click').on('click', async function (e) {
            e.preventDefault();
            datahandling.clearForm(self.form);
            $('#reg_user_db_id').val('');
            self.resetEditOnlyText();
            // Enable role selection when adding
            $('#reg_user_type').prop('disabled', false);
            // Make Employee Code editable when adding
            $('#reg_emp_code').prop('readonly', false);
            // Always show employee code search button when adding
            $('#search_emp_btn').show();
            $('#searched_emp_code_container').hide();
            // Reset all error states except role
            const fields = [
                {input: 'reg_location', feedback: 'locationFeedback'},
                {input: 'reg_emp_code', feedback: 'empCodeFeedback'},
                {input: 'reg_first_name', feedback: 'firstNameFeedback'},
                {input: 'reg_last_name', feedback: 'lastNameFeedback'},
                {input: 'reg_password', feedback: 'passwordFeedback'}
            ];
            fields.forEach(f => {
                const input = document.getElementById(f.input);
                const feedback = document.getElementById(f.feedback);
                if (input) input.classList.remove('is-invalid');
                if (feedback) {
                    feedback.style.display = '';
                    feedback.textContent = '';
                }
            });
            container.showModal(self.modal);
        });

        // Save
        $('#submit_user_btn').off('click').on('click', async function(e) {
            // Enable user_type field before submit so its value is sent
            $('#reg_user_type').prop('disabled', false);
            e.preventDefault();
            // Only require searched emp code for roles that use it, but skip for Admin/Receptionist on edit
            const selectedRoleNum = Number($('#reg_user_type').val());
            const searchedEmpCode = $('#searched_emp_code').val();
            const isEditing = Boolean($('#reg_user_db_id').val());
            let hasError = false;
            // Validate Role
            const roleInput = document.getElementById('reg_user_type');
            const roleFeedback = document.getElementById('roleFeedback');
            if (roleInput && !roleInput.value) {
                roleInput.classList.add('is-invalid');
                roleFeedback.style.display = 'block';
                roleFeedback.textContent = 'Role is required';
                hasError = true;
            } else if (roleInput) {
                roleInput.classList.remove('is-invalid');
                roleFeedback.style.display = '';
                roleFeedback.textContent = '';
            }
            // Validate Location
            const locationInput = document.getElementById('reg_location');
            const locationFeedback = document.getElementById('locationFeedback');
            const requiresFixedLocation = selectedRoleNum !== 3;
            if (locationInput && requiresFixedLocation && !locationInput.value) {
                locationInput.classList.add('is-invalid');
                locationFeedback.style.display = 'block';
                locationFeedback.textContent = 'Location is required';
                hasError = true;
            } else if (locationInput) {
                locationInput.classList.remove('is-invalid');
                locationFeedback.style.display = '';
                locationFeedback.textContent = '';
            }
            // Validate Employee Code (if shown)
            const empCodeInput = document.getElementById('reg_emp_code');
            const empCodeFeedback = document.getElementById('empCodeFeedback');
            if ($('#emp_code_container').is(':visible') && empCodeInput && !empCodeInput.value.trim()) {
                empCodeInput.classList.add('is-invalid');
                empCodeFeedback.style.display = 'block';
                empCodeFeedback.textContent = 'Employee Code is required';
                hasError = true;
            } else if (empCodeInput) {
                empCodeInput.classList.remove('is-invalid');
                empCodeFeedback.style.display = '';
                empCodeFeedback.textContent = '';
            }
            // Validate First Name (if shown)
            const firstNameInput = document.getElementById('reg_first_name');
            const firstNameFeedback = document.getElementById('firstNameFeedback');
            if ($('#employee_name_container').is(':visible') && firstNameInput && !firstNameInput.value.trim()) {
                firstNameInput.classList.add('is-invalid');
                firstNameFeedback.style.display = 'block';
                firstNameFeedback.textContent = 'First Name is required';
                hasError = true;
            } else if (firstNameInput) {
                firstNameInput.classList.remove('is-invalid');
                firstNameFeedback.style.display = '';
                firstNameFeedback.textContent = '';
            }
            // Validate Last Name (if shown)
            const lastNameInput = document.getElementById('reg_last_name');
            const lastNameFeedback = document.getElementById('lastNameFeedback');
            if ($('#employee_name_container').is(':visible') && lastNameInput && !lastNameInput.value.trim()) {
                lastNameInput.classList.add('is-invalid');
                lastNameFeedback.style.display = 'block';
                lastNameFeedback.textContent = 'Last Name is required';
                hasError = true;
            } else if (lastNameInput) {
                lastNameInput.classList.remove('is-invalid');
                lastNameFeedback.style.display = '';
                lastNameFeedback.textContent = '';
            }
            // Validate Password (if shown)
            const passwordInput = document.getElementById('reg_password');
            const passwordFeedback = document.getElementById('passwordFeedback');
            if ($('#password_container').is(':visible') && passwordInput && !passwordInput.value.trim() && !isEditing) {
                passwordInput.classList.add('is-invalid');
                passwordFeedback.style.display = 'block';
                passwordFeedback.textContent = 'Password is required';
                hasError = true;
            } else if (passwordInput) {
                passwordInput.classList.remove('is-invalid');
                passwordFeedback.style.display = '';
                passwordFeedback.textContent = '';
            }
            if (hasError) return;
            // Only require searched emp code for roles that use it, but skip for Admin/Receptionist on edit
            if (selectedRoleNum === 3) {
                // Guard: clear emp code before saving
                $('#reg_emp_code').val('');
            } else if ((selectedRoleNum === 1 || selectedRoleNum === 2) && isEditing) {
                // Admin/Receptionist on edit: use current value, no search required
                // Do nothing, keep current value
            } else {
                if (!searchedEmpCode) {
                    Triggers.showToast('Please search and confirm a valid employee code before saving.', 'Register User', 1);
                    return;
                }
                $('#reg_emp_code').val(searchedEmpCode);
            }
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
                // Disable role selection when editing
                $('#reg_user_type').prop('disabled', true);
                // Make Employee Code readonly when editing
                $('#reg_emp_code').prop('readonly', true);
                // Hide employee code search for Admin and Receptionist on edit
                const roleId = Number(response.role_id);
                if (roleId === 1 || roleId === 2) {
                    $('#search_emp_btn').hide();
                    $('#searched_emp_code_container').hide();
                } else {
                    $('#search_emp_btn').show();
                }

                // Fill usertype
                $('#reg_user_type').trigger('change');

                // Fill location
                // Always use 'locations' for consistency
                const locations = response.location_id || response.locations || [];
                await self.location_dropdown(locations);
                if (Array.isArray(locations)) {
                    $('#reg_location').val(locations).trigger('change');
                } else if (locations) {
                    $('#reg_location').val([locations]).trigger('change');
                } else {
                    $('#reg_location').val('').trigger('change');
                }

                // Fill name
                $('#employee_name_container').removeClass('d-none');
                $('#reg_first_name').val(response.first_name || '');
                $('#reg_last_name').val(response.last_name || '');

                // Fill user type
                const roleName = (response.role_name || '').toLowerCase();
                if (roleName === 'guard') {
                    $('#reg_first_name, #reg_last_name').prop('readonly', false);
                    // Remove WFH option for Guard
                    $('#reg_location').find('option').filter(function() {
                        return $(this).val().toLowerCase().includes('wfh') || $(this).text().toLowerCase().includes('wfh');
                    }).remove();
                } else {
                    $('#reg_first_name, #reg_last_name').prop('readonly', true);
                    // Remove WFH option for Receptionist
                    if (roleName === 'receptionist') {
                        $('#reg_location').find('option').filter(function() {
                            return $(this).val().toLowerCase().includes('wfh') || $(this).text().toLowerCase().includes('wfh');
                        }).remove();
                    }
                }

                // Reset all error states including role
                const fields = [
                    {input: 'reg_user_type', feedback: 'roleFeedback'},
                    {input: 'reg_location', feedback: 'locationFeedback'},
                    {input: 'reg_emp_code', feedback: 'empCodeFeedback'},
                    {input: 'reg_first_name', feedback: 'firstNameFeedback'},
                    {input: 'reg_last_name', feedback: 'lastNameFeedback'},
                    {input: 'reg_password', feedback: 'passwordFeedback'}
                ];
                fields.forEach(f => {
                    const input = document.getElementById(f.input);
                    const feedback = document.getElementById(f.feedback);
                    if (input) input.classList.remove('is-invalid');
                    if (feedback) {
                        feedback.style.display = '';
                        feedback.textContent = '';
                    }
                });

                // Show the 'Leave blank to keep current password' message in edit mode
                $('.edit-only-text').show();

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

    async keylistener() {
        const input = document.getElementById("reg_emp_code");

            input.addEventListener("keydown", (e) => {
            // Allow control keys
            const allowedKeys = [
                "Backspace", "Delete", "ArrowLeft", "ArrowRight", "Tab"
            ];

            if (allowedKeys.includes(e.key)) return;

            if (!/^[0-9]$/.test(e.key)) {
                e.preventDefault();
            }
            });
            input.addEventListener("input", () => {
            input.value = input.value.replace(/\D/g, "");
            });
    }

}
const instance = new UsersTable();
instance.InitializePage();

export default instance;