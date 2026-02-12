import { Modal, Dropdown } from 'bootstrap';
import container from './common/container';
import datahandling from './common/datahandling';
import triggers from './common/triggers';
import settable from './common/settable';
import component from './common/component';
import $ from 'jquery';

$(document).ready(function() {

let URL = '/userTypes/';

// Allow Bootstrap dropdown menus to render without clipping inside responsive tables.
    const $usersTableWrapper = $('#userTypeTable').closest(
        '.table-responsive, .table-responsive-sm, .table-responsive-md, .table-responsive-lg'
    );
    if ($usersTableWrapper.length) {
        $usersTableWrapper.css('overflow', 'visible');
    }

    // Ensure dropdown toggles work even when rows are injected by DataTables.
    $(document).on('click', '.dropdown-toggle', function (event) {
        event.preventDefault();
        const dropdown = Dropdown.getOrCreateInstance(this);
        dropdown.toggle();
    });


const usertypemodal = document.getElementById('addTypeModal');
const userTypeModal = new Modal(usertypemodal);

$('#openAddTypePopup').click(function() { 
    const input = document.getElementById('edit_type_name');
    input.value = '';
    delete input.dataset.id;
    
    $('#modalTitle').text('Add User Type');
    $('#save_type').text('Save New User Type');
    
    // Use Bootstrap's show() instead of jQuery's fadeIn()
    userTypeModal.show(); 
});

$(document).on('click', '.edit-type', function() {
    let id = $(this).data('id');
    const input = document.getElementById('edit_type_name');
    
    $('#save_type').text('Update');
    $('#modalTitle').text('Edit User Type');

    $.get(URL+'usertype/' + id + '/edit', function(data) {
        input.value = data.name;
        input.dataset.id = data.id; 
        
        // CHANGE THIS: Replace .fadeIn(200) with the Bootstrap show method
        userTypeModal.show();
    });
});

document.getElementById('save_type').addEventListener('click', () => {
    const input = document.getElementById('edit_type_name');
    const id = input.dataset.id;
    const user_type = input.value.trim();
    const $btn = $('#save_type');

    if (!user_type) {
        alert('User type name cannot be empty.');
        return;
    }

    $btn.prop('disabled', true).text(id === undefined ? 'Saving...' : 'Updating...');

    if (id === undefined) {
        $.ajax({
            url: URL+"addusertype",
            type: 'POST',
            data: {
                user_type: user_type,
                // _token: $('meta[name="csrf-token"]').attr('content')
                _token: window.Laravel.csrfToken
            },

            success: function (response) {
                const message = response.success || "User Type Added Successfully!";
                
                // 1. Set the Title (Optional but looks better)
                $('.toast-title').text("Success");
                
                // 2. Set the Body Text
                $('#toastMessageforadd').text(message);

                // 3. Show the Toast
                const toastElement = document.getElementById('SUCCESSTOAST');
                if (toastElement) {
                    const toast = new bootstrap.Toast(toastElement);
                    toast.show();
                }

                // 4. Reload
                // setTimeout(() => {
                //     location.reload();
                // }, 1500);

                if ($.fn.DataTable.isDataTable('#userTypeTable')) {
                $('#userTypeTable').DataTable().draw(false);

                }
                // $btn.prop('disabled', false).text('Save New User Type');
                userTypeModal.hide(); 
},
            error: function (xhr) {
                $btn.prop('disabled', false).text('Save New User Type');
                alert(xhr.responseJSON?.message ?? 'Save failed.');
            }
        });
    } else {
        $.ajax({
            url: URL+'usertype/' + id,
            type: 'POST',
            data: {
                id: id,
                user_type: user_type,
                _method: 'PUT',
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                const message = response.success || "User Type Updated Successfully!";
                
                // 1. Set the Title (Optional but looks better)
                $('.toast-title').text("Success");
                
                // 2. Set the Body Text
                $('#toastMessageforadd').text(message);

                // 3. Show the Toast
                const toastElement = document.getElementById('SUCCESSTOAST');
                if (toastElement) {
                    const toast = new bootstrap.Toast(toastElement);
                    toast.show();
                }
                // setTimeout(() => {
                //     location.reload();
                // }, 1500);

                if ($.fn.DataTable.isDataTable('#userTypeTable')) {
                $('#userTypeTable').DataTable().draw(false);

                }
                $btn.prop('disabled', false).text('Update');
                userTypeModal.hide(); 
            },
            error: function (xhr) {
                $btn.prop('disabled', false).text('Update');
                alert(xhr.responseJSON?.message ?? 'Edit failed.');
            }
        });
    }
});

$('#closeAddType').click(function() { $('#addTypeModal').fadeOut(200); });
const deleteModalEl = document.getElementById('notificationContainer');
const deleteModal = new Modal(deleteModalEl);

let roleIdToDelete = null;

// Open Delete Modal
$(document).on('click', '.delete-type', function() {
    roleIdToDelete = $(this).data('id'); // Grab the ID from the button
    
    // Update the notification text dynamically
    $('#notification-title').text('Confirm Deletion');
    $('#notification-message').text('Are you sure you want to delete this user type?');
    
    // Reset button state in case it was stuck on "Deleting..."
    $('#btn_ok').prop('disabled', false).text('Yes');

    // Show the modal
    deleteModal.show();
});

// Handle "Yes" Button Click
$('#btn_ok').on('click', function() {
    if (!roleIdToDelete) return;

    const btn = $(this);
    btn.prop('disabled', true).text('Deleting...');

    $.ajax({
        url: URL+'usertype/' + roleIdToDelete,
        type: 'DELETE',
        data: { 
            _token: window.Laravel.csrfToken 
        },
        success: function(response) {
            // Hide the confirmation modal
            deleteModal.hide();

            // Set message and fire the toast
            $('#DeletetoastMessage').text(response.success || "User Type Deleted Successfully!");
            const toastElement = document.getElementById('DELETE');
            
            if (toastElement) {
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
            }

            // Reload after toast
            // setTimeout(function() {
            //     location.reload();
            // }, 1500); 

            if ($.fn.DataTable.isDataTable('#userTypeTable')) {
                $('#userTypeTable').DataTable().draw(false);

                }
                // $btn.prop('disabled', false).text('Yes');
                userTypeModal.hide(); 
        },
        error: function(xhr) {
            alert("Error deleting role: " + (xhr.responseJSON?.message || "Internal Server Error"));
            btn.prop('disabled', false).text('Yes');
            deleteModal.hide();
        }
    });
});









});

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
            10,          // pagination
            {},           // data
            false
        );

        $(self.table).on('init.dt', function () {

            console.log('DATATABLE INITIALIZED');

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


}
const userType = new UserTypeTable();
userType.onLoadPage();