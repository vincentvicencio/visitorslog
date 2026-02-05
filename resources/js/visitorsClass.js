import container from './common/container';
import datahandling from './common/datahandling';
import triggers from './common/triggers';
import settable from './common/settable';
import component from './common/component';
import $ from 'jquery';

console.log('🔥 visitors.js file loaded');

class VisitorsLogTable {
    constructor() {
        this.defaultFields  = []
        this.url            = "/visitorslog/"
        this.table          = "#visitorsLogTable"
        this.module         = "visitorslog"
        this.form           = "#"
        this.modal          = "#"
        this.formid         = "#"  
    }

    async onLoadPage(){
        this.list();
    }

    async list() {
        const self = this;

        const tableHeader = [
            { id: "full_name",    label: "Personal Details" },
            { id: "visitor_type", label: "Visitor Type" },
            { id: "visitor_id",   label: "ID No." },
            { id: "image",        label: "Image" },
            { id: "visit",        label: "Visit" },
            { id: "time",         label: "Time" },
            { id: "creator",      label: "By" },
            { id: "status",       label: "Status" },
            { id: "action",       label: "Action" },
        ];

        const columns = tableHeader.map(col => ({
            data: col.id,
            title: col.label
        }));

        const columnDefs = [
            { targets: [0, 1, 2, 3], orderable: false }
        ];

        console.log('🚀 BEFORE createTableAjax');

        // ✅ ADD `{ dom: 'rtip' }` to REMOVE default search input
        settable.createTableAjax(
            self.table,
            columns,
            self.url,
            columnDefs,
            10,
            {},
            false // 🔥 THIS REMOVES THE DEFAULT SEARCH BAR
        );

        console.log('🚀 AFTER createTableAjax');

        // =====================================================
        // 🧪 DEBUG: LOG AJAX RESPONSE
        // =====================================================
        $(self.table).on('xhr.dt', function (e, settings, json) {
            console.log('✅ AJAX RESPONSE:', json);
        });

        // =====================================================
        // ✅ INIT COMPLETE (SAFE API ACCESS)
        // =====================================================
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

/* =====================================================
   ✅ THIS WAS MISSING (CRITICAL)
   ===================================================== */
const visitorsLog = new VisitorsLogTable();
visitorsLog.onLoadPage();
