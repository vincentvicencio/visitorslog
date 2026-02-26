import container from './common/container';
import datahandling from './common/datahandling';
import Triggers from './common/triggers';
import settable from './common/settable';

class UserTypeTable {
    constructor() {
        this.defaultFields  = []
        this.url            = "/userTypes/"
        this.table          = "#userTypeTable"
        this.module         = "usertype"
        this.form           = "#add_type_form"
        this.modal          = "#addTypeModal"
    }

    async initializePage(){

        this.list();
        this.initializeButtons();
        this.keylistener();
    }
    async list() {
        const self = this;

        const tableHeader = [
            { id: "name",       label: "Name"         },
            { id: "created_by", label: "Created By"   },
            { id: "updated_by", label: "Updated By"   },
            { id: "created_at", label: "Created Date" },
            { id: "action",     label: "Action"       },
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

            // Redraw table after initialization to ensure proper rendering
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
    async keylistener() {
        const input = document.getElementById("name");

        input.addEventListener("keydown", (e) => {
            // Allow control keys
            const allowedKeys = [
                "Backspace", "Delete", "ArrowLeft", "ArrowRight", "Tab"
            ];

            if (allowedKeys.includes(e.key)) return;

            // Block anything that's not a letter or space
            if (!/^[a-zA-Z\s]$/.test(e.key)) {
                e.preventDefault();
            }
        });

        // Catch paste, drag-drop, autofill, etc.
        input.addEventListener("input", () => {
            input.value = input.value.replace(/[^a-zA-Z\s]/g, "");
        });
    }
}

const instance = new UserTypeTable();
instance.initializePage();

export default instance;