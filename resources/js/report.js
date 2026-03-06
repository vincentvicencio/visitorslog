import triggers from './common/triggers';
import * as bootstrap from 'bootstrap';
import $ from 'jquery';
import container from './common/container';
import datahandling from './common/datahandling';
import settable from './common/settable';

window.reportFilters = {
    date_from: '',
    date_to: '',
    visitor_type: ''
};


let tableReloadInterval = null;

// Define the URL 
let URL = '/reports/';

// --- SETTABLE FUNCTION OVERRIDE FOR FILTERING --- 
$(document).ready(function(){
    // --- HANDLE DELETE BUTTON CLICK ---
    $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        const name = $(this).data('name') || "this visitor"; 
        if (!id) return;
        openDeleteModal(id, name);
    });
    // --- INITIALIZE DATATABLE ---
    $(document).on('click', '#openFilterBtn', function () {
        const modalEl = document.getElementById('filterModal');
        const modalInstance =
            bootstrap.Modal.getInstance(modalEl) ||
            new bootstrap.Modal(modalEl);

        modalInstance.show();
    });
    // --- EXPORT TO EXCEL ---
    $(document).on('click', '#exportReportBtn', function () {
        try {
            const filters = window.reportFilters || {};
            
            // Build query string with filters
            const params = new URLSearchParams();
            if (filters.date_from) params.append('date_from', filters.date_from);
            if (filters.date_to) params.append('date_to', filters.date_to);
            if (filters.visitor_type) params.append('visitor_type', filters.visitor_type);
            
            const searchValue = $('#typeSearch').val();
            if (searchValue) params.append('search', searchValue);

            // Trigger download
            const exportUrl = '/reports/export?' + params.toString();
            window.location.href = exportUrl;
            
            // Show success toast
            triggers.showToast('Exporting report to Excel...', 'Exporting', 0);
        } catch (error) {
            console.error('Export error:', error);
            triggers.showToast('Failed to export report. Please try again.', 1);
        }
    });

    $(document).on('submit', '#filterForm', function(e) {
        e.preventDefault();

        Object.assign(window.reportFilters, {
            date_from: $('input[name="date_from"]').val(),
            date_to: $('input[name="date_to"]').val(),
            visitor_type: $('select[name="visitor_type"]').val()
        });

        if ($.fn.DataTable.isDataTable('#reportTable')) {
            $('#reportTable').DataTable().draw(); 
        }

        const filterModal = document.getElementById('filterModal');
        const modalInstance = bootstrap.Modal.getInstance(filterModal);

        if (modalInstance) {
            modalInstance.hide();
        }
    });

    // Handle Reset Button
    $(document).on('click', '.btn-secondary[href*="/report"]', function(e) {
        e.preventDefault();

        // Reset
        $('#filterForm')[0].reset();

        // Reset filters
        Object.assign(window.reportFilters, {
            date_from: '',
            date_to: '',
            visitor_type: ''
        });

        const filterModal = document.getElementById('filterModal');
        const modalInstance = bootstrap.Modal.getInstance(filterModal);

        if (modalInstance) {
            modalInstance.hide();
        }

        // Reload table with no filters
        $('#reportTable').DataTable().draw();
        
    });


}); 



    $(document).on('click', '#viewBtn', function () {
        let visitorId = $(this).data('id');
        let type = $(this).data('type');

        if (!visitorId) return;

        $.ajax({
            url: "/visitorslog/view",
            type: "POST",
            data: {
                id: visitorId,
                type: type,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                window.location.href = response.redirect;
            },
            error: function (xhr, status, error) {
                let msg = 'Something went wrong.';
                let title = 'Error';
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    const m = xhr.responseJSON.message.toLowerCase();
                    if (m.includes('csrf')) {
                        msg = 'Please refresh the page and log in again.';
                        title = 'Session Expired';
                    } else {
                        msg = xhr.responseJSON.message;
                    }
                }
                Triggers.showToast(msg, title, 1);
            }
        });
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.Laravel.csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

class ReportClassTable {
    constructor() {
        this.defaultFields  = []
        this.url            = "/reports/"
        this.table          = "#reportTable"
        this.module         = "reports"
        this.form           = "#"
        this.modal          = "#"
        this.formid         = "#"  
    }

    async onLoadPage(){
        this.empList();
        this.initializeButtons();
    }

    async initializePage(){
        this.list();
        this.initializeButtons();
    }
    async list() {
        const self = this;

        const tableHeader = [
            { id: "full_name", label: "Name" },
            { id: "location", label: "Location" },
            { id: 'contact_number', label: 'Contact No.' },
            { id: "visitor_type",       label: "Visitor Type" },
            { id: "visitor_id",       label: "ID No." },
            { id: "visit",   label: "Visit Date" },
            { id: "time_in", label: "Time In" },
            { id: "time_out", label: "Time Out" },
            { id: "logged_by", label: "Time In by" },
            { id: "updated_by", label: "Timed Out by" },
            { id: "status",   label: "Status" },
            { id: "action",         label: "Action" },
        ];

        const columns = tableHeader.map(col => ({
            data: col.id, 
            title: col.label,
        }));

        const columnDefs = [
            { targets: [0, 1, 2, 3], orderable: false }
        ];

        settable.createTableAjax(
            self.table,
            columns,
            self.url, // Use the full path directly
            columnDefs,
            self.module,
            10,
            window.reportFilters,
            false
        );

        $(self.table)
            .off('init.dt')
            .on('init.dt', function () {
                const tableApi = $(self.table).DataTable();

                // tableApi.draw();
                // tableApi.on('draw', function () {
                //     $(tableApi.table().container()).find('.dataTables_scrollHeadInner').css('width', '100%');
                //     $(tableApi.table().node()).css('width', '99%');
                // });

                if (tableReloadInterval) {
                    clearInterval(tableReloadInterval);
                }

                tableReloadInterval = setInterval(() => {
                    if ($.fn.DataTable.isDataTable('#reportTable')) {
                        $('#reportTable').DataTable().ajax.reload(null, false);
                    }
                }, 5000);

                // =========================================
                // CUSTOM SEARCH
                // =========================================
                $('#typeSearch')
                    .off('keyup')
                    .on('keyup', function () {
                        tableApi.draw();
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

        setTimeout(() => {
            const searchInput = document.getElementById('dt-search-0');             
                if (searchInput) {
                    searchInput.setAttribute('placeholder', 'Search here...');
                }
        },  100);
    }

    async empList() {
        const self = this;

        const tableHeader = [
                { id: "emp_code", label: "Employee Code" },
                { id: "full_name", label: "Name" },
                { id: "location", label: "Location" },
                { id: "log_date", label: "Log Date" },
                { id: "time_in", label: "Time In" },
                { id: "time_out", label: "Time Out" },
                { id: "creator", label: "Logged by" },
                { id: "updated_by", label: "Timed Out by" },
                { id: "status", label: "Status" },
                { id: "action", label: "Action" },
            ];

        const columns = tableHeader.map(col => ({
            data: col.id, 
            title: col.label,
        }));

        const columnDefs = [
            { targets: [0, 1, 2, 3], orderable: false }
        ];

        settable.createTableAjax(
            self.table,
            columns,
            self.url+"emp", // Use the full path directly
            columnDefs,
            self.module,
            10,
            window.reportFilters,
            false
        );

        $(self.table)
            .off('init.dt')
            .on('init.dt', function () {
                const tableApi = $(self.table).DataTable();

                // tableApi.draw();
                // tableApi.on('draw', function () {
                //     $(tableApi.table().container()).find('.dataTables_scrollHeadInner').css('width', '100%');
                //     $(tableApi.table().node()).css('width', '99%');
                // });

                

                if (tableReloadInterval) {
                    clearInterval(tableReloadInterval);
                }

                tableReloadInterval = setInterval(() => {
                    if ($.fn.DataTable.isDataTable('#reportTable')) {
                        $('#reportTable').DataTable().ajax.reload(null, false);
                    }
                }, 5000);

                // =========================================
                // CUSTOM SEARCH
                // =========================================
                $('#typeSearch')
                    .off('keyup')
                    .on('keyup', function () {
                        tableApi.draw();
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

        setTimeout(() => {
            const searchInput = document.getElementById('dt-search-0');             
                if (searchInput) {
                    searchInput.setAttribute('placeholder', 'Search here...');
                }
        },  100);
    }

    async initializeButtons(){
        const self = this
        // $('#btn_add').off('click').on('click', async function (e) {
        //     e.preventDefault()
        //     datahandling.clearForm(self.form)
        //     container.showModal('#addTypeModal')
        // })
        
        $(document).off('click', '#btn_submit').on('click', '#btn_submit', async function(e) {
            e.preventDefault();
            const formid    = self.form;
            const formdata  = new FormData($(formid)[0]);
            await Triggers.removeErrorOnInput(formid);
            await datahandling.saveForm(self.url + 'save', self.table, self.form, formdata)
        });
    }
}
const instance = new ReportClassTable();
instance.initializePage();
export default instance;