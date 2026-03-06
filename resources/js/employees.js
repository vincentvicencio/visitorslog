import settable from './common/settable';
import container from './common/container';
import datahandling from './common/datahandling';
import Triggers from './common/triggers';
import { Modal } from 'bootstrap';

const deleteModalEl = document.getElementById('notificationContainer');
const deleteModal = new Modal(deleteModalEl);

let tableReloadInterval = null;

let URL = '/employeeslog/';

    class EmployeesTable {
        constructor() {
            this.defaultFields  = []
            this.url            = "/employeeslog/"
            this.table          = "#visitorsLogTable"
            this.module         = "employeeslog"
            this.form           = "#"
            this.modal          = "#"
            this.formid         = "#"
            this.originalSearchTerm = "" // Store original search term
        }

    async onLoadPage(){
        this.list();
        this.initializeButtons();
        this.initializeEmployeeSearchButton();
        this.keylistener();
    }


    async list() {
        const self = this;

        const tableHeader = [
                { id: "emp_code", label: "Employee Code" },
                { id: "full_name", label: "Name" },
                { id: "location", label: "Location" },
                { id: "log_date", label: "Log Date" },
                { id: "time_in", label: "Time In" },
                { id: "time_out", label: "Time Out" },
                { id: "creator", label: "Logged by" },
                { id: "status", label: "Status" },
                { id: "action", label: "Action" },
            ];

        const columns = tableHeader.map(col => ({
            data: col.id, 
            title: col.label
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

            // tableApi.draw();

            if (tableReloadInterval) {
                clearInterval(tableReloadInterval);
            }

            tableReloadInterval = setInterval(() => {
                if ($.fn.DataTable.isDataTable('#visitorsLogTable')) {
                    $('#visitorsLogTable').DataTable().ajax.reload(null, false);
                }
            }, 5000);

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

        // Initialize buttons and their event listeners
        async initializeButtons() {
            const self = this;

            // Add Button
            // $('#addBtnEmp').off('click').on('click', async function (e) {
            //     e.preventDefault();
            //     // Clear the form fields
            //     $('#logemp_emp_code').val('');
            //     $('#logemp_first_name').val('');
            //     $('#logemp_last_name').val('');
            //     $('#employee_name_container').addClass('d-none');
            //     $('#searched_emp_code').val('');
            //     $('#searched_emp_code_container').hide();
            //     self.hideEmployeeDropdown();
            //     container.showModal('#logempModal')
            // })

            $(document).on('click', '#empTimeoutBtn', function () {
                let Id = $(this).data('id');
                Triggers.showNotification(
                    '#notificationContainer',
                    'Time Out',
                    'Are you sure you want to time out this Employee?',
                    Id
                );
            });

            
    
            $(document).on('click', '#empViewBtn', function () {
                let visitorId = $(this).data('id');
                let type = $(this).data('type');

                if (!visitorId) {
                    Triggers.showToast('Invalid Employee Code.', 1);
                    return;
                }


                $.ajax({
                    url: URL+"view",
                    type: "POST",
                    data: {
                        id: visitorId,
                        type: type,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    timeout: 15000, 
                    success: function (response) {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            Triggers.showToast('No redirect URL provided.', 1);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('View error:', error, xhr);
                        let msg = 'Unable to load visitor details.';
                        
                        if (status === 'timeout') {
                            msg = 'Request timeout. Please try again.';
                        } else if (xhr.responseJSON?.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.status === 404) {
                            msg = 'Visitor not found.';
                        } else if (xhr.status >= 500) {
                            msg = 'Server error. Please try again later.';
                        }
                        
                        Triggers.showToast(msg, 1);
                    }
                });
            });

            $(document).on('click', '#timeout_btn', function () {
                let Id = $('#record_id').val();
                
                if (!Id) {
                    Triggers.showToast('Invalid record ID.', 'Invalid', 1);
                    return;
                }
                $.ajax({
                    url: URL+"timeout",
                    type: "POST",
                    data: {
                        id: Id,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    timeout: 15000,
                    success: function (response) {
                        Triggers.showToast(response.message, 'Success', 0);
                        setTimeout(() => {
                            $('.toast').fadeOut('slow');
                                deleteModal.hide();
                            
                        }, 1000);
                        if ($.fn.DataTable.isDataTable('#visitorsLogTable')) {
                            $('#visitorsLogTable').DataTable().draw(false);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Timeout error:', error, xhr);
                        let msg = 'TimeOut failed.';
                        
                        if (status === 'timeout') {
                            msg = 'Request timeout. Please try again.';
                        } else if (xhr.responseJSON?.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.status === 404) {
                            msg = 'Record not found.';
                        } else if (xhr.status >= 500) {
                            msg = 'Server error. Please try again later.';
                        }
                        
                        Triggers.showToast(msg, 'Error', 1);
                    }
                });
            });



            // Submit Log Employee Button
            $('#submit_logemp_btn').off('click').on('click', async function (e) {
                e.preventDefault();
                await self.submitEmployeeLog();
            })

            // Clear error message while typing
            const nameInput = document.getElementById('valididtypeName');
            const nameFeedback = document.getElementById('valididtypeNameFeedback');
            if (nameInput && nameFeedback) {
                nameInput.addEventListener('input', function() {
                    if (nameInput.value.trim()) {
                        nameInput.classList.remove('is-invalid');
                        nameFeedback.style.display = '';
                        nameFeedback.textContent = '';
                    }
                });
            }
        }

        // Load form data for editing
        async onLoadForm(record_id) {
            const self = this;
    
            const url = `${self.url}search`;
            const response = await datahandling.processData(
                url,
                'POST',
                { id: record_id }
            );

            // put values into the form fields scoped inside our modal
            $("#valididtype_record_id").val(record_id);
            $("#valididtypeName").val(response.data.name);

            // Reset error state
            const nameInput = document.getElementById('valididtypeName');
            const nameFeedback = document.getElementById('valididtypeNameFeedback');
            if (nameInput) nameInput.classList.remove('is-invalid');
            if (nameFeedback) {
                nameFeedback.style.display = '';
                nameFeedback.textContent = '';
            }

            container.showModal(self.modal);
        }

    async keylistener() {
        const input = document.getElementById("valididtypeName");

        if(input){
            input.addEventListener("keydown", (e) => {
                // Allow control keys
                const allowedKeys = [
                    "Backspace", "Delete", "ArrowLeft", "ArrowRight", "Tab"
                ];

                if (allowedKeys.includes(e.key)) return;

                // Block anything that's not a letter or space
                // This blocks 0-9 but allows letters and ALL symbols
                if (/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            });

            // Catch paste, drag-drop, autofill, etc.
            input.addEventListener("input", () => {
                // allow letters, spaces, apostrophes and hyphens
                input.value = input.value.replace(/[^a-zA-Z\s\-\']/g, "");
            });
        }
    }

        async handleEmployeeSearchClick(event) {
            const searchTerm = $('#logemp_emp_code').val().trim();
            // Require non-empty search term
            if (!searchTerm) {
                Triggers.showToast('Please enter an employee code or name.', 'Employee Search', 1);
                return;
            }
    
            // Store original search term
            this.originalSearchTerm = searchTerm;
    
            const $btn = $(event.currentTarget);
            $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');
    
            $.ajax({
                url: '/employeeslog/search-employees',
                type: 'GET',
                data: { q: searchTerm },
                timeout: 10000,
                success: (response) => {
                    const results = response.results || [];
                    
                    // Check for exact emp code match
                    const exactMatch = results.find(emp => emp.id.toLowerCase() === searchTerm.toLowerCase());
                    
                    if (exactMatch) {
                        // Exact emp code found
                        this.selectEmployee(exactMatch);
                    } else if (results.length === 0) {
                        // No results
                        $('#logemp_first_name').val('');
                        $('#logemp_last_name').val('');
                        $('#employee_name_container').addClass('d-none');
                        $('#searched_emp_code').val('');
                        $('#searched_emp_code_container').hide();
                        this.hideEmployeeDropdown();
                        Triggers.showToast('Employee not found.','Employee Search', 1);
                    } else if (results.length === 1) {
                        // Single name match - auto-select
                        this.selectEmployee(results[0]);
                        this.hideEmployeeDropdown();
                    } else {
                        // Multiple name matches - show dropdown suggestions
                        this.showEmployeeDropdown(results);
                    }
                    $btn.prop('disabled', false).html('<i class="bi bi-search"></i>');
                },
                error: () => {
                    Triggers.showToast('Failed to search employee.','Employee Search', 1);
                    $btn.prop('disabled', false).html('<i class="bi bi-search"></i>');
                }
            });
        }

        selectEmployee(employee) {
            // For name searches, preserve the original search term in the search field
            // For emp code searches, use the emp code
            const searchTerm = this.originalSearchTerm;
            const isExactCodeMatch = searchTerm.toLowerCase() === employee.id.toLowerCase();
            
            if (isExactCodeMatch) {
                $('#logemp_emp_code').val(employee.id);
            } else {
                // Keep original search term (name search)
                $('#logemp_emp_code').val(searchTerm);
            }
            
            $('#logemp_first_name').val(employee.first_name || '');
            $('#logemp_last_name').val(employee.last_name || '');
            $('#employee_name_container').removeClass('d-none');
            $('#searched_emp_code').val(employee.id);
            $('#searched_emp_code_container').show();
            this.hideEmployeeDropdown();
            Triggers.showToast('Employee selected!','Employee Search', 0);
        }

        showEmployeeDropdown(results) {
            const self = this;
            
            // Remove existing dropdown if present
            $('#employeeDropdown').remove();
            
            // Create dropdown HTML with unique IDs for each item
            const dropdownHtml = `
                <div id="employeeDropdown" class="list-group position-absolute w-100" style="top: 100%; left: 0; z-index: 1050; max-height: 300px; overflow-y: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: 1px solid #ddd; background: white;">
                    ${results.map((emp, idx) => `
                        <div class="list-group-item list-group-item-action employee-option" style="cursor: pointer;" data-emp-idx="${idx}">
                            <strong>${emp.id}</strong> - ${emp.first_name} ${emp.last_name}
                        </div>
                    `).join('')}
                </div>
            `;
            
            // Get the input container and add dropdown
            const $inputGroup = $('#logemp_emp_code').closest('.input-group');
            $inputGroup.css('position', 'relative');
            $inputGroup.after(dropdownHtml);
            
            // Handle employee option clicks
            $('.employee-option').each((index, el) => {
                el.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const idx = parseInt($(el).data('emp-idx'));
                    const employee = results[idx];
                    
                    if (employee) {
                        self.selectEmployee({
                            id: employee.id,
                            first_name: employee.first_name,
                            last_name: employee.last_name
                        });
                    }
                }, false);
            });
        }

        hideEmployeeDropdown() {
            $('#employeeDropdown').remove();
        }

        async submitEmployeeLog() {
            // Validate required fields
            const empCode = $('#searched_emp_code').val().trim();
            const firstName = $('#logemp_first_name').val().trim();
            const lastName = $('#logemp_last_name').val().trim();

            if (!empCode) {
                Triggers.showToast('Please search and select an employee.', 'Employee Log', 1);
                return;
            }

            if (!firstName || !lastName) {
                Triggers.showToast('Employee name is incomplete.', 'Employee Log', 1);
                return;
            }

            // Prepare form data
            const formData = new FormData($('#logemp_form')[0]);
            formData.append('emp_code', empCode);
            formData.append('first_name', firstName);
            formData.append('last_name', lastName);
            formData.append('full_name', firstName + ' ' + lastName);

            try {
                const response = await datahandling.processData(
                    this.url + 'save',
                    'POST',
                    Object.fromEntries(formData)
                );

                if (response.status === 0) {
                    Triggers.showToast(response.message, response.title, 0);
                    // Close modal and refresh table
                    container.hideModal('#logempModal');
                    // Clear form
                    $('#logemp_form')[0].reset();
                    $('#logemp_emp_code').val('');
                    $('#logemp_first_name').val('');
                    $('#logemp_last_name').val('');
                    $('#employee_name_container').addClass('d-none');
                    $('#searched_emp_code').val('');
                    $('#searched_emp_code_container').hide();
                    // Reload table
                    if ($.fn.DataTable.isDataTable('#visitorsLogTable')) {
                        $('#visitorsLogTable').DataTable().ajax.reload(null, false);
                    }
                } else {
                    Triggers.showToast(response.message, response.title || 'Error', 1);
                }
            } catch (error) {
                console.error('Error:', error);
                Triggers.showToast('Failed to log employee.', 'Employee Log', 1);
            }
        }
        initializeEmployeeSearchButton() {
            const searchBtn = document.getElementById('search_emp_btn');
            if (!searchBtn) return;
            searchBtn.removeEventListener('click', this.handleEmployeeSearchClick);
            searchBtn.addEventListener('click', this.handleEmployeeSearchClick.bind(this));
        }


}

const instance = new EmployeesTable();
// instance.initializePage();
export default instance;