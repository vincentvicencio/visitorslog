import container from './common/container';
import datahandling from './common/datahandling';
import Triggers from './common/triggers';
import settable from './common/settable';
import component from './common/component';

class UserTypeTable {
    constructor() {
        this.defaultFields  = []
        // first parameter of your route
        this.url            = "/userTypes/"
        // id name of your table listing in user
        this.table          = "#userTypeTable"
        // module
        this.module         = "usertype"
        // form id
        this.form           = "#add_type_form"
        // offCanvas
        this.modal          = "#addTypeModal"
    }

    async initializePage(){

        this.list();
        this.initializeButtons();
    }
    async list() {
        const self = this;

        const tableHeader = [
            { id: "name",       label: "Name" },
            { id: "created_by", label: "Created By" },
            { id: "updated_by", label: "Updated By" },
            { id: "created_at", label: "Created Date" },
            { id: "action",     label: "Action" },
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
            10,          // pagination
            {},           // data
            false
        );

        $(self.table).on('init.dt', function () {

            const tableApi = $(self.table).DataTable();

            // FORCE DRAW
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
        $('#btn_add').off('click').on('click', async function (e) {
            e.preventDefault()
                datahandling.clearForm(self.form)
                container.showModal('#addTypeModal')
        })
        
        $(document).off('click', '#btn_submit').on('click', '#btn_submit', async function(e) {
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
        $("#name").val(response.data.name);

        container.showModal(self.modal);
    }

}

const instance = new UserTypeTable();
instance.initializePage();

export default instance;