import container from './common/container';
import datahandling from './common/datahandling';
import triggers from './common/triggers';
import settable from './common/settable';
import component from './common/component';
import $ from 'jquery';


class UserTypeTable {
    constructor() {
        this.defaultFields  = []
        // first parameter of your route
        this.url            = "/userTypes/"
        // id name of your table listing in user
        this.table          = "#userTypeTable"
        // module
        this.module         = "userTypes"
        // form id
        this.form           = "#"
        // offCanvas
        this.modal          = "#"
        // add user form id
        this.formid         = "#"  
    }

    async onLoadPage(){
        this.list();
    }
    async list() {
        const self = this;

        const tableHeader = [
            { id: "name",       label: "Name" },
            { id: "created_by",       label: "Created By" },
            { id: "updated_by",      label: "Updated By" },
            { id: "created_at",   label: "Created Date" },
            { id: "action",         label: "Action" },
        ];

        const columns = tableHeader.map(col => ({
            data: col.id, 
            title: col.label,
            width: 'auto'
        }));

        const columnDefs = [
            { targets: [0, 1, 2, 3], orderable: false }
        ]; 

        settable.createTableAjax(
            self.table,
            columns,
            self.url,
            columnDefs,
            10,          // ✅ pagination
            {}           // ✅ data
        );

        const tableApi = $(self.table).DataTable();
        $('#usertypesearch').off('keyup').on('keyup', function() {
            tableApi.search(this.value).draw();
        });

         setTimeout(() => {
            const searchInput = document.getElementById('dt-search-0');             
                if (searchInput) {
                    searchInput.setAttribute('placeholder', 'Search here...');
                }
            }, 100);

    }


}
const userType = new UserTypeTable();
userType.onLoadPage();

export default userType;
