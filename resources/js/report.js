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

// prefix url
let URL = '/reports/';

$(document).ready(function(){
    // =================================Dropdown==================================
// Allow Bootstrap dropdown menus to render without clipping inside responsive tables.
    

    // =================================Dropdown==================================
    // =================================Buttons==================================
    

    $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        const name = $(this).data('name') || "this visitor"; // Assuming you have data-name in your button
        
        if (!id) return;
        
        openDeleteModal(id, name);
    });

    $(document).on('click', '#openFilterBtn', function () {
        const modalEl = document.getElementById('filterModal');
        const modalInstance =
            bootstrap.Modal.getInstance(modalEl) ||
            new bootstrap.Modal(modalEl);

        modalInstance.show();
    });

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

        // Reset form UI
        $('#filterForm')[0].reset();

        // IMPORTANT: clear the global filters
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


}); // End of document.ready

    $(document).on('click', '.view-button', function(e) {
        // 1. Prevent the page from reloading
        e.preventDefault();
        
        // 2. Get the image URL from the data-image attribute
        const imageUrl = $(this).data('image');
        
        // 3. Set the src of the image inside the modal
        $('#modalImage').attr('src', imageUrl);
        
        // 4. Show the modal
        $('#View_imageModal').modal('show');
    });

    $('#View_imageModal').on('hidden.bs.modal', function () {
        $('#modalImage').attr('src', ''); 
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
            error: function (xhr) {
                let msg = 'Unable to load visitor details.';
                if (xhr.responseJSON?.message) {
                    msg = xhr.responseJSON.message;
                }
                Triggers.showToast(msg, 1);
            }
        });
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.Laravel.csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

// Initialize Modal
const notificationModalEl = document.getElementById('notificationContainer');
const notificationModal = new bootstrap.Modal(notificationModalEl);

// --- OPEN DELETE MODAL FUNCTION ---

export function openDeleteModal(id, name = "this record") {
    const recordInput = document.getElementById('record_id');
    const messageTitle = document.getElementById('notification-title');
    const messageBody = document.getElementById('notification-message');

    // Set Data
    recordInput.value = id; 
    
    // UI Updates
    messageTitle.innerText = "Confirm Deletion";
    messageBody.innerText = `Are you sure you want to delete ${name}?`;
    
    // Reset button state in case it was disabled previously
    $('#btn_ok').prop('disabled', false).text('Yes');

    notificationModal.show();
}

// --- HANDLE DELETE SUBMIT ---

document.getElementById('btn_ok').addEventListener('click', function() {
    const id = document.getElementById('record_id').value;
    const $btn = $(this);

    if (!id) {
        Triggers.showToast('Invalid record ID.', 1);
        return;
    }

    $btn.prop('disabled', true).text('Processing...');

    // AJAX request to delete the record
    $.ajax({
        url:URL+'delete-visitor/' + id,
        type: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {

            // 1. Hide the confirmation modal
            notificationModal.hide();

            // 2. Set the text and show the manual Bootstrap Toast (#DELETE)
            $('#DeletetoastMessage').text(response.success || "Report Log Deleted Successfully!");
            
            const toastElement = document.getElementById('DELETE');
            if (toastElement) {
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
            }

            // refresh the datatable only
            if ($.fn.DataTable.isDataTable('#reportTable')) {
                $('#reportTable').DataTable().draw(false);
            }

            // 3. Re-enable the button
            $btn.prop('disabled', false).text('Yes');
            const modalEl = document.getElementById('filterModal');
            const modalInstance =
                bootstrap.Modal.getInstance(modalEl) ||
                new bootstrap.Modal(modalEl);

            modalInstance.hide();
        },
        error: function (xhr) {
            // Re-enable button on error
            $btn.prop('disabled', false).text('Yes');
            
            const errorMsg = xhr.responseJSON?.message ?? 'Delete failed.';
            Triggers.showToast(errorMsg, 1);
        }
    });
});


class ReportClassTable {
    constructor() {
        this.defaultFields  = []
        // first parameter of your route
        this.url            = "/reports/"
        // id name of your table listing in user
        this.table          = "#reportTable"
        // module
        this.module         = "reports"
        // form id
        this.form           = "#"
        // offCanvas
        this.modal          = "#"
        // add user form id
        this.formid         = "#"  
    }

    async initializePage(){
        this.list();
        this.initializeButtons();
    }
    async list() {
        const self = this;

        const tableHeader = [
            { id: "full_name",       label: "Personal Details"},
            { id: "visitor_type",    label: "Visitor Type"    },
            { id: "visitor_id",      label: "ID No."          },
            { id: "image",           label: "Image"           },
            { id: "visit",           label: "Visit"           },
            { id: "time",            label: "Time"            },
            { id: "creator",         label: "By"              },
            { id: "status",          label: "Status"          },
            { id: "action",          label: "Action"          },
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
            self.url,
            columnDefs,
            self.module,            // module
            10,                   // pagination
            window.reportFilters, // data
            false                 // enableSearch
        );

        $(self.table)
            .off('init.dt')
            .on('init.dt', function () {
                const tableApi = $(self.table).DataTable();

                tableApi.draw();
                tableApi.on('draw', function () {
                    $(tableApi.table().container()).find('.dataTables_scrollHeadInner').css('width', '100%');
                    $(tableApi.table().node()).css('width', '99%');
                });

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
}
const instance = new ReportClassTable();
instance.initializePage();

export default instance;