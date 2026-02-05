import container from './common/container';
import datahandling from './common/datahandling';
import triggers from './common/triggers';
import settable from './common/settable';
import component from './common/component';
import $ from 'jquery';


class ReportClassTable {
    constructor() {
        this.defaultFields  = []
        // first parameter of your route
        this.url            = "/reporttable/"
        // id name of your table listing in user
        this.table          = "#reportTable"
        // module
        this.module         = "reporttable"
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
            { id: "full_name",       label: "Personal Details" },
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
            {},          // ✅ data
            false
        );

        $(self.table).on('init.dt', function () {

            console.log('✅ DATATABLE INITIALIZED');

            const tableApi = $(self.table).DataTable();

            // 🔥 FORCE DRAW
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


}
const reportlog = new ReportClassTable();
reportlog.onLoadPage();

export default reportlog;
