import container from './common/container';
import datahandling from './common/datahandling';
import triggers from './common/triggers';
import settable from './common/settable';
import component from './common/component';
import $ from 'jquery';



// Initialize global filter object
        window.reportFilters = {
            date_from: '',
            date_to: '',
            visitor_type: ''
        };


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

        const tableElement = $(self.table);
        tableElement.DataTable().clear().destroy();
        
        const table = tableElement.DataTable({
            pageLength: 10,
            autoWidth: false,
            scrollX: true,
            scrollCollapse: true,
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: false,
            pagingType: 'simple',
            dom: '<"top">rt<"bottom"pi><"clear">',
            stateLoadParams: function (settings, data) {
                data.length = 10;
            },
            ajax: {
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: window.location.origin + self.url + 'list',
                type: "POST",
                data: function (d) { 
                    d.search = $("#typeSearch").val();
                    d.date_from = window.reportFilters.date_from;
                    d.date_to = window.reportFilters.date_to;
                    d.visitor_type = window.reportFilters.visitor_type;
                }
            },
            language: {
                paginate: {
                    next: '<span aria-hidden="true">&gt;</span>',
                    previous: '<span aria-hidden="true">&lt;</span>'
                },
                lengthMenu: "_MENU_",
                search: ""
            },
            columns: columns,
            columnDefs: columnDefs,
            drawCallback: function () {
                const api = this.api();
                $(api.table().container()).find('.dataTables_scrollHeadInner').css('width', '100%');
                $(api.table().node()).css('width', '100%');
                component.initializeButtons(self.table, self.url);
            },
            initComplete: function() {
                this.api().columns.adjust();
                // Remove duplicate header created by scrollX
                $('.dt-scroll-head').remove();
            }
        });

        // =========================================
        // CUSTOM SEARCH
        // =========================================
        $('#typeSearch')
            .off('keyup')
            .on('keyup', function () {
                table.draw();
            });

        // =========================================
        // ENTRIES PER PAGE
        // =========================================
        $('#entriesPerPage')
            .off('change')
            .on('change', function () {
                table.page.len(this.value).draw();
            });

        setTimeout(() => {
            const searchInput = document.getElementById('dt-search-0');             
                if (searchInput) {
                    searchInput.setAttribute('placeholder', 'Search here...');
                }
            }, 100);

    }


}
const reportlog = new ReportClassTable();
reportlog.onLoadPage();

export default reportlog;
