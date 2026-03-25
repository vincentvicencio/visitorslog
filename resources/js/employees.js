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
        this.bindEmployeeDropdownDismissHandlers();
        this.keylistener();
    }


    async list() {
        const self = this;

        const tableHeader = [
                { id: "emp_code", label: "Employee Code" },
                { id: "full_name", label: "Name" },
                { id: "location", label: "Location" },
                { id: "log_date", label: "Log Date" },
                { id: "time", label: "Time" },
                { id: "activity", label: "Activity" },
                { id: "status", label: "Status" },
                { id: "creator", label: "Logged by" },
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

           // Add Button — now redirects to employeeslog.form page (modal commented out)
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
                    
                    if (exactMatch && results.length === 1) {
                        // Only auto-populate on an unambiguous exact emp code match
                        this.selectEmployee(exactMatch);
                    } else if (results.length === 0) {
                        // No results
                        $('#logemp_full_name').val('');
                        // $('#middle_name').val('');
                        // $('#logemp_last_name').val('');
                        // $('#employee_name_container').addClass('d-none');
                        $('#searched_emp_code').val('');
                        $('#searched_emp_code_container').hide();
                        $('#logemp_profile_pic').attr('src', '').hide();
                        this.hideEmployeeDropdown();
                        Triggers.showToast('Employee not found.','Employee Search', 1);
                    } else {
                        // Always show dropdown so the user can confirm their selection
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
            
            // $('#logemp_first_name').val(employee.first_name || '');
            // $('#middle_name').val(employee.middle_name || '');
            // $('#logemp_last_name').val(employee.last_name || '');
            const fullName = [employee.first_name, employee.middle_name, employee.last_name]
            .filter(part => part && part.trim() !== '')
            .join(' ')
            .replace(/\s+/g, ' ')
            .trim();
            $('#logemp_full_name').val(fullName);
            // $('#employee_name_container').removeClass('d-none');
            $('#searched_emp_code').val(employee.id);

            if (employee.profile_pic) {
                $('#photoPreview').css('display', 'block').attr('src', employee.profile_pic);
                $('#image_path').val(employee.profile_pic);
                $('#webcam').css('display', 'none');
            } else {
                $('#photoPreview').css('display', 'none').attr('src', '');
                $('#image_path').val('');
                $('#webcam').css('display', 'block');
            }

            // $('#searched_emp_code_container').show();
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
            
            // Get the input container and append dropdown inside it so top:100% anchors correctly
            const $inputContainer = $('#logemp_emp_code').closest('.input-group, .search-bar');
            if ($inputContainer.length) {
                $inputContainer.css('position', 'relative');
                $inputContainer.append(dropdownHtml);
            } else {
                const $parent = $('#logemp_emp_code').parent();
                $parent.css('position', 'relative');
                $parent.append(dropdownHtml);
            }
            
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
                            middle_name: employee.middle_name,
                            last_name: employee.last_name,
                            profile_pic: employee.profile_pic
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
            // const firstName = $('#logemp_first_name').val().trim();
            // const lastName = $('#logemp_last_name').val().trim();
            const fullName = $('#logemp_full_name').val().trim();
            const nameParts = fullName.split(' ').filter(Boolean);
            const firstName = nameParts[0] || '';
            const lastName = nameParts.slice(1).join(' ') || '';

            if (!empCode) {
                Triggers.showToast('Please search and select an employee.', 'Employee Log', 1);
                return;
            }

            if (!fullName) {
            Triggers.showToast('Employee name is incomplete.', 'Employee Log', 1);
            return;
            }

            if (!firstName || !lastName) {
                Triggers.showToast('Employee name is incomplete.', 'Employee Log', 1);
                return;
            }

            // Use a plain payload here because processData uses $.ajax default serialization.
            // Passing Blob/File values via Object.fromEntries(FormData) can throw Illegal invocation.
            const payload = {
            emp_code: empCode,
            full_name: fullName,
            image_path: $('#image_path').val() || '',
            activity: $('#activity').val(),
            status: $('#status').val(),
            };

            try {
                const response = await datahandling.processData(
                    this.url + 'save',
                    'POST',
                    payload
                );

                if (response.status === 0) {
                    Triggers.showToast(response.message, response.title, 0);
                    // Clear form
                    $('#logemp_form')[0].reset();
                    $('#logemp_emp_code').val('');
                    // $('#logemp_first_name').val('');
                    // $('#middle_name').val('');
                    // $('#logemp_last_name').val('');
                    // $('#employee_name_container').addClass('d-none');
                    $('#searched_emp_code').val('');
                    $('#searched_emp_code_container').hide();
                    $('#photoPreview').css('display', 'none').attr('src', '');
                    $('#image_path').val('');
                    $('#webcam').css('display', 'block');
                    this.hideEmployeeDropdown();
                    // Close modal only when on the log page
                    if (document.getElementById('logempModal')) {
                        container.hideModal('#logempModal');
                    }
                    // Reload table if on the log page
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
        initializeFormPage() {
            this.initializeEmployeeSearchButton();
            this.bindEmployeeDropdownDismissHandlers();
            const self = this;

            $('#logemp_form').off('submit').on('submit', async function (e) {
                e.preventDefault();
                await self.submitEmployeeLog();
            });

            $(document).off('click', '#clrBtn').on('click', '#clrBtn', function () {
                $('#logemp_form')[0].reset();
                $('#photoPreview').css('display', 'none').attr('src', '');
                $('#image_path').val('');
                $('#imageInput').val('');
                startWebcam();
            });
        }

        bindEmployeeDropdownDismissHandlers() {
            const self = this;
            // Close suggestions when clicking anywhere outside the search area/dropdown.
            $(document)
                .off('mousedown.employeeDropdown')
                .on('mousedown.employeeDropdown', function (e) {
                    const $target = $(e.target);
                    const insideDropdown = $target.closest('#employeeDropdown').length > 0;
                    const insideSearch = $target.closest('#logemp_emp_code, #search_emp_btn').length > 0;

                    if (!insideDropdown && !insideSearch) {
                        self.hideEmployeeDropdown();
                    }
                });

            $('#logemp_emp_code')
                .off('keydown.employeeDropdown')
                .on('keydown.employeeDropdown', function (e) {
                    if (e.key === 'Escape') {
                        self.hideEmployeeDropdown();
                    }
                })
                .off('input.employeeDropdown')
                .on('input.employeeDropdown', function () {
                    self.hideEmployeeDropdown();
                });
        }

        initializeEmployeeSearchButton() {
            const searchBtn = document.getElementById('search_emp_btn');
            if (!searchBtn) return;
            searchBtn.removeEventListener('click', this.handleEmployeeSearchClick);
            searchBtn.addEventListener('click', this.handleEmployeeSearchClick.bind(this));
        }


}

const instance = new EmployeesTable();

$(document).ready(function () {
    if (document.getElementById('logemp_form') && !document.getElementById('visitorsLogTable')) {
        instance.initializeFormPage();
    }
});

export default instance;