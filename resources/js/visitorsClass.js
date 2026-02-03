import container from './common/container';
import datahandling from './common/datahandling';
import triggers from './common/triggers';
import settable from './common/settable';
import component from './common/component';
import $ from 'jquery';


class VisitorsLogTable {
    constructor() {
        this.defaultFields  = []
        // first parameter of your route
        this.url            = "/visitorslog/"
        // id name of your table listing in user
        this.table          = "#visitorsLogTable"
        // module
        this.module         = "visitorslog"
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
            // { id: "personal_detail",       label: "Personal Details" },
            { id: "visitor_type",       label: "Visitor Type" },
            { id: "visitor_id",       label: "ID No." },
            { id: "image",      label: "Image" },
            { id: "visit",   label: "Visit" },
            { id: "time",   label: "Time" },
            { id: "creator",   label: "By" },
            { id: "status",   label: "Status" },
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
        $('input[type="search"]').off('keyup').on('keyup', function() {
            // console.log('tangina mo charle')
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
const visitorsLog = new VisitorsLogTable();
visitorsLog.onLoadPage();

export default visitorsLog;
