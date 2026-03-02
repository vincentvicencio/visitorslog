import settable from './common/settable';
import container from './common/container';
import datahandling from './common/datahandling';
import Triggers from './common/triggers';

    class ValidIDTypeTable {
        constructor() {
            this.defaultFields  = []
            // base path for the Laravel routes (prefix in web.php is "IDtype")
            this.url            = "/IDtype/"
            this.table          = "#valididtypeTable"
            // module name must match the JS filename (used by component.js loader)
            this.module         = "idtype"
            this.form           = "#valididtypeForm"
            this.modal          = "#valididtypeModal"

        }

    async initializePage(){
        this.list();
        this.initializeButtons();
        this.keylistener();
    }


    async list() {
        const self = this;

        const tableHeader = [
            // server returns `name` property mapped to id_type_name
            { id: "id_type_name", label: "Valid ID Type"},
            { id: "created_by",   label: "Created By"   },
            { id: "updated_by",   label: "Updated By"   },
            { id: "created_at",   label: "Created Date" },
            { id: "action",       label: "Action"       },
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

        // Initialize buttons and their event listeners
        async initializeButtons() {
            const self = this

            // Add Button
            $('#addBtn').off('click').on('click', async function (e) {
                e.preventDefault();
                datahandling.clearForm(self.form);
                // Reset Valid ID Type error state
                const nameInput = document.getElementById('valididtypeName');
                const nameFeedback = document.getElementById('valididtypeNameFeedback');
                if (nameInput) nameInput.classList.remove('is-invalid');
                if (nameFeedback) {
                    nameFeedback.style.display = '';
                    nameFeedback.textContent = '';
                }
                container.showModal(self.modal);
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

            // Save Button
            $(document).off('click', '#valididtypeSubmit').on('click', '#valididtypeSubmit', async function(e) {
                e.preventDefault();

                const formid   = self.form;
                const formdata = new FormData($(formid)[0]);

                // Bootstrap validation for valid ID type
                const nameInput = document.getElementById('valididtypeName');
                const nameFeedback = document.getElementById('valididtypeNameFeedback');
                if (!nameInput.value.trim()) {
                    nameInput.classList.add('is-invalid');
                    nameFeedback.style.display = 'block';
                    nameFeedback.textContent = 'Valid ID Type is required';
                    return;
                } else {
                    nameInput.classList.remove('is-invalid');
                    nameFeedback.style.display = '';
                    nameFeedback.textContent = '';
                }

                await Triggers.removeErrorOnInput(formid);
                await datahandling.saveForm(self.url + 'save', self.table, self.form, formdata);
            });
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

const instance = new ValidIDTypeTable();
instance.initializePage();

export default instance;