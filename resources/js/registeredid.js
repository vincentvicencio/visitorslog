import $ from 'jquery';
import Triggers from './common/triggers';
import settable from './common/settable';
import container from './common/container';
import datahandling from './common/datahandling';

    // RegisterIdTable
    class RegisterIdTable {
        constructor() {
            this.defaultFields  = [];
            this.url            = "/registerId/";
            this.table          = "#registerIdTable";
            this.module         = "registeredid";
            this.form           = "#textInputForm";
            this.modal          = "#registerIDModal";
            this.reloadInterval = null;
        }
        
    async initializePage(){
        this.list();
        this.initializeButtons();
        this.keylistener();
    }

    // List of Register ID
    async list() {
        const self = this;

        const tableHeader = [
            { id: "visitor_type",       label: "Name" },
            { id: "id_number",       label: "ID Number" },
            { id: "created_by",       label: "Created By" },
            { id: "updated_by",      label: "Updated By" },
            { id: "created_at",   label: "Created Date" },
            { id: "updated_at",   label: "Updated Date" },
            { id: "action",         label: "Action" },
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

            // Prevent multiple intervals
            if (self.reloadInterval) {
                clearInterval(self.reloadInterval);
            }
            self.reloadInterval = setInterval(() => {
                tableApi.ajax.reload(null, false);
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

    // DataTable Initialization
    async initializeButtons(){
            // Clear Visitor ID error on input
            const visitorIdInput = document.getElementById('name');
            const visitorIdFeedback = document.getElementById('nameFeedback');
            if (visitorIdInput && visitorIdFeedback) {
                visitorIdInput.addEventListener('input', function() {
                    if (visitorIdInput.value.trim()) {
                        visitorIdInput.classList.remove('is-invalid');
                        visitorIdFeedback.style.display = '';
                        visitorIdFeedback.textContent = '';
                    }
                });
            }
            // Clear Visitor Type error on change
                const visitorTypeInput = document.getElementById('visitortype');
                const visitorTypeFeedback = document.getElementById('visitorTypeFeedback');
                if (visitorTypeInput && visitorTypeFeedback) {
                    visitorTypeInput.addEventListener('change', function() {
                        if (visitorTypeInput.value) {
                            visitorTypeInput.classList.remove('is-invalid');
                            visitorTypeFeedback.style.display = '';
                            visitorTypeFeedback.textContent = '';
                        }
                    });
                }
            // Clear ID Location error on change
                const locationInputField = document.getElementById('visitorIDLocation');
                const locationFeedbackField = document.getElementById('visitorIDLocationFeedback');
                if (locationInputField && locationFeedbackField) {
                    locationInputField.addEventListener('change', function() {
                        if (locationInputField.value) {
                            locationInputField.classList.remove('is-invalid');
                            locationFeedbackField.style.display = '';
                            locationFeedbackField.textContent = '';
                        }
                    });
                }
        const self = this
        $('#addBtn').off('click').on('click', async function (e) {
            e.preventDefault()
            datahandling.clearForm(self.form)
            // Reset Visitor ID error state
            const visitorIdInput = document.getElementById('name');
            const visitorIdFeedback = document.getElementById('nameFeedback');
            if (visitorIdInput) visitorIdInput.classList.remove('is-invalid');
            if (visitorIdFeedback) {
                visitorIdFeedback.style.display = '';
                visitorIdFeedback.textContent = '';
            }
            // Reset Visitor Type error state
            const visitorTypeInput = document.getElementById('visitortype');
            const visitorTypeFeedback = document.getElementById('visitorTypeFeedback');
            if (visitorTypeInput) visitorTypeInput.classList.remove('is-invalid');
            if (visitorTypeFeedback) {
                visitorTypeFeedback.style.display = '';
                visitorTypeFeedback.textContent = '';
            }
            // Reset ID Location error state
            const locationInput = document.getElementById('visitorIDLocation');    
            const locationFeedback = document.getElementById('visitorIDLocationFeedback');
            if (locationInput) locationInput.classList.remove('is-invalid');
            if (locationFeedback) {
                locationFeedback.style.display = '';
                locationFeedback.textContent = '';
            }
            container.showModal(self.modal)
        })

        $(document).off('click', '#registerIDSubmit').on('click', '#registerIDSubmit', async function(e) {
            e.preventDefault();

            const formid    = self.form;
            const formdata  = new FormData($(formid)[0]);

            // Bootstrap validation for Visitor ID
            const visitorIdInput = document.getElementById('name');
            const visitorIdFeedback = document.getElementById('nameFeedback');
            let hasError = false;
            if (!visitorIdInput.value.match(/^\d+$/)) {
                visitorIdInput.classList.add('is-invalid');
                visitorIdFeedback.style.display = 'block';
                visitorIdFeedback.textContent = 'Visitor ID Is Required';
                hasError = true;
            } else {
                visitorIdInput.classList.remove('is-invalid');
                visitorIdFeedback.style.display = '';
                visitorIdFeedback.textContent = '';
            }

            // Bootstrap validation for Visitor Type
            const visitorTypeInput = document.getElementById('visitortype');
            const visitorTypeFeedback = document.getElementById('visitorTypeFeedback');
            if (!visitorTypeInput.value) {
                visitorTypeInput.classList.add('is-invalid');
                visitorTypeFeedback.style.display = 'block';
                visitorTypeFeedback.textContent = 'Visitor Type Is Required';
                hasError = true;
            } else {
                visitorTypeInput.classList.remove('is-invalid');
                visitorTypeFeedback.style.display = '';
                visitorTypeFeedback.textContent = '';
            }
            // Bootstrap validation for ID Location
            const locationInput = document.getElementById('visitorIDLocation');    
            const locationFeedback = document.getElementById('visitorIDLocationFeedback');
            if (!locationInput.value) {
                locationInput.classList.add('is-invalid');
                locationFeedback.style.display = 'block';
                locationFeedback.textContent = 'ID Location Is Required';
                hasError = true;
            } else {
                locationInput.classList.remove('is-invalid');
                locationFeedback.style.display = '';
                locationFeedback.textContent = '';
            }
            if (hasError) return;

            await Triggers.removeErrorOnInput(formid);
            await datahandling.saveForm(self.url + 'save', self.table, self.form, formdata)
        });
    }

    // for Edit Button
    async onLoadForm(record_id) {
        const self = this;

        const url = `${self.url}search`;
        const response = await datahandling.processData(
            url,
            'POST',
            { id: record_id }
        );

        $("#record_id").val(record_id);
        $("#name").val(response.data.id_number);
        $("#visitortype").val(response.data.visitor_type);
        $("#visitorIDLocation").val(response.data.location);

        // Reset Visitor ID error state
        const visitorIdInput = document.getElementById('name');
        const visitorIdFeedback = document.getElementById('nameFeedback');
        if (visitorIdInput) visitorIdInput.classList.remove('is-invalid');
        if (visitorIdFeedback) {
            visitorIdFeedback.style.display = '';
            visitorIdFeedback.textContent = '';
        }
        // Reset Visitor Type error state
        const visitorTypeInput = document.getElementById('visitortype');
        const visitorTypeFeedback = document.getElementById('visitorTypeFeedback');
        if (visitorTypeInput) visitorTypeInput.classList.remove('is-invalid');
        if (visitorTypeFeedback) {
            visitorTypeFeedback.style.display = '';
            visitorTypeFeedback.textContent = '';
        }
        // Reset ID Location error state
        const locationInput = document.getElementById('visitorIDLocation');    
        const locationFeedback = document.getElementById('visitorIDLocationFeedback');
        if (locationInput) locationInput.classList.remove('is-invalid');
        if (locationFeedback) {
            locationFeedback.style.display = '';
            locationFeedback.textContent = '';
        }

        container.showModal(self.modal);
    }


    async keylistener() {
        const input = document.getElementById("name");

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
    
const instance = new RegisterIdTable();
instance.initializePage();

export default instance;




