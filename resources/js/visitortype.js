import settable from './common/settable';
import container from './common/container';
import datahandling from './common/datahandling';
import Triggers from './common/triggers';

    class VisitorTypeTable {
        constructor() {
            this.defaultFields  = []
            this.url            = "/visitortype/"
            this.table          = "#visitorsTable"
            this.module         = "visitortype"
            this.form           = "#textInputForm"
            this.modal          = "#textInputModal"

        }

    async initializePage(){
        this.list();
        this.initializeButtons();
    }


    async list() {
        const self = this;

        const tableHeader = [
            { id: "name",        label: "Name"         },
            { id: "created_by",  label: "Created By"   },
            { id: "updated_by",  label: "Updated By"   },
            { id: "created_at",  label: "Created Date" },
            { id: "action",      label: "Action"       },
        ];

        const columns = tableHeader.map(col => ({
            data: col.id, 
            title: col.label
        }));

        const columnDefs = [
            { targets: [0, 1, 2, 3], orderable: false }
        ]; 

<<<<<<< HEAD
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
=======
        settable.createTableAjax(
            self.table,
            columns,
            self.url,
            columnDefs,
            self.module,
            10,          // pagination
            {},          // data
            false
        );
>>>>>>> 75c44668e6d6ef9ac1cfee5591451d7eb505707e

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

<<<<<<< HEAD
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
                e.preventDefault()
=======
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

    async initializeButtons() {
        const self = this
            
        $('#addBtn').off('click').on('click', async function (e) {
            e.preventDefault()
>>>>>>> 75c44668e6d6ef9ac1cfee5591451d7eb505707e
                datahandling.clearForm(self.form)
                container.showModal(self.modal)
        })
                    
<<<<<<< HEAD
            // Save Button
            $(document).off('click', '#textInputSubmit').on('click', '#textInputSubmit', async function(e) {
                e.preventDefault();
=======
        $(document).off('click', '#textInputSubmit').on('click', '#textInputSubmit', async function(e) {
            e.preventDefault();
>>>>>>> 75c44668e6d6ef9ac1cfee5591451d7eb505707e
                
            const formid    = self.form;
            const formdata  = new FormData($(formid)[0]);
        
<<<<<<< HEAD
                await Triggers.removeErrorOnInput(formid);
                await datahandling.saveForm(self.url + 'save', self.table, self.form, formdata)
            });
        }

        // Load form data for editing
        async onLoadForm(record_id) {
            const self = this;
=======
            await Triggers.removeErrorOnInput(formid);
            await datahandling.saveForm(self.url + 'save', self.table, self.form, formdata)
        });
    }
    async onLoadForm(record_id) {
        const self = this;
>>>>>>> 75c44668e6d6ef9ac1cfee5591451d7eb505707e
    
            const url = `${self.url}search`;
            const response = await datahandling.processData(
                url,
                'POST',
                { id: record_id }
            );
    
            $("#record_id").val(record_id);
            $("#name").val(response.data.name);
    
            container.showModal(self.modal);
<<<<<<< HEAD
        }


=======
    }
>>>>>>> 75c44668e6d6ef9ac1cfee5591451d7eb505707e
}

const instance = new VisitorTypeTable();
instance.initializePage();

export default instance;