import $ from 'jquery';
import Triggers from './common/triggers';
import settable from './common/settable';
import container from './common/container';
import datahandling from './common/datahandling';

    // RegisterIdTable
    class RegisterIdTable {
        constructor() {
            this.defaultFields  = []
            this.url            = "/registerId/"
            this.table          = "#registerIdTable"
            this.module         = "registeredid"
            this.form           = "#textInputForm"
            this.modal          = "#registerIDModal"
        }
        
    async initializePage(){
        this.list();
        this.initializeButtons();
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
            const self = this
            $('#addBtn').off('click').on('click', async function (e) {
                e.preventDefault()
                    datahandling.clearForm(self.form)
                    container.showModal(self.modal)
            })
            
            $(document).off('click', '#registerIDSubmit').on('click', '#registerIDSubmit', async function(e) {
                e.preventDefault();
                
                const formid    = self.form;
                const formdata  = new FormData($(formid)[0]);
    
                
                await Triggers.removeErrorOnInput(formid);
                await datahandling.saveForm(self.url + 'save', self.table, self.form, formdata)
    
            });
        }
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
        
            container.showModal(self.modal);
        }


    // DataTable Initialization
    async initializeButtons(){
        const self = this
        $('#addBtn').off('click').on('click', async function (e) {
            e.preventDefault()
                datahandling.clearForm(self.form)
                container.showModal(self.modal)
        })
        
        $(document).off('click', '#registerIDSubmit').on('click', '#registerIDSubmit', async function(e) {
            e.preventDefault();
            
            const formid    = self.form;
            const formdata  = new FormData($(formid)[0]);

            
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
    
            container.showModal(self.modal);
    }

}
    
const instance = new RegisterIdTable();
instance.initializePage();

export default instance;




