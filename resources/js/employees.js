import settable from './common/settable';
import container from './common/container';
import datahandling from './common/datahandling';
import Triggers from './common/triggers';

    class EmployeesTable {
         constructor() {
            this.defaultFields  = []
            this.url            = "/employeeslog/"
            this.table          = "#visitorsLogTable"
            this.module         = "employeeslog"
            this.form           = "#"
            this.modal          = "#"
            this.formid         = "#"  
        }

    async initializePage(){
        this.list();
        this.initializeButtons();
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

            setInterval(() => {

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
            $('#addBtnEmp').off('click').on('click', async function (e) {
                e.preventDefault();
                datahandling.clearForm(self.form);
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


}

const instance = new EmployeesTable();

export default instance;