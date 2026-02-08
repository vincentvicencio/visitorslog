import { Modal } from 'bootstrap';
import container from './common/container';
import datahandling from './common/datahandling';
import Triggers from './common/triggers';
import settable from './common/settable';
import component from './common/component';
import $ from 'jquery';

// Get modal element
const textInputModalEl = document.getElementById('textInputModal');
const textInputModal = new Modal(textInputModalEl);
const deleteModalEl = document.getElementById('notificationContainer');
const deleteModal = new Modal(deleteModalEl);

let URL = '/visitortype/';

// Open modal function
export function openTextInputModal(id, name) {
    const input = document.getElementById('userInput');
    input.value = name;            // set current name
    input.dataset.id = id;         // store id in data attribute
    textInputModal.show();
}
export function openTextInputModalBlank() {
    const input = document.getElementById('userInput');
    input.value = '';
    textInputModal.show();
}

document.getElementById('textInputSubmit').addEventListener('click', () => {
    const input = document.getElementById('userInput');
    const id = input.dataset.id;
    const visitor_type = input.value.trim();
    if (!visitor_type) {
        Triggers.showToast('Please enter a visitor type name.', 1);
        return;
    }

    if(id === undefined) {
        $.ajax({
            url: URL+"save",
            type: 'POST',
            data: {
                visitor_type: visitor_type,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Triggers.showToast(response.message, 0);
                setTimeout(() => {
                    $(textInputModal.hide()).fadeOut('slow');
                }, 1000);
                if ($.fn.DataTable.isDataTable('#visitorsTable')) {
                    $('#visitorsTable').DataTable().draw(false);
                }
            },
            error: function (xhr) {
                Triggers.showToast(xhr.responseJSON?.message ?? 'Failed to save visitor type.', 1);
                const input = document.getElementById('userInput');
                input.value = '';
            }
        });
    }else{
        $.ajax({
            url: URL+"edit",
            type: 'POST',
            data: {
                id: id,
                visitor_type: visitor_type,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Triggers.showToast(response.message, 0);
                setTimeout(() => {
                    $(textInputModal.hide()).fadeOut('slow');
                }, 1000);
                if ($.fn.DataTable.isDataTable('#visitorsTable')) {
                    $('#visitorsTable').DataTable().draw(false);
                }

            },
            error: function (xhr) {
                Triggers.showToast(xhr.responseJSON?.message ?? 'Failed to update visitor type.', 1);
                const input = document.getElementById('userInput');
                input.value = '';
            }
        });
    }
    
});

$(document).ready(function () {
    $(document).on('click', '#addBtn', function () {
        openTextInputModalBlank();
    });

    $(document).on('click', '#editBtn', function () {
        const id = $(this).data('id');
        const name = $(this).data('name'); 
        if (!id) return;
        openTextInputModal(id, name);
    });



    $(document).on('click', '#deleteBtn', function () {
        let id = $(this).data('id');

        Triggers.showNotification(
            '#notificationContainer',
            'Delete Visitor Type',
            'Are you sure you want to delete this visitor type?',
            id
        );
    });
    $(document).on('click', '#btn_ok', function () {
        let id = $('#record_id').val();
        $.ajax({
            url: URL+"delete",
            type: "POST",
            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Triggers.showToast(response.message, 0);
                setTimeout(() => {
                    $('.toast').fadeOut('slow');
                    $(deleteModal.hide()).fadeOut('slow');
                }, 1000);
                if ($.fn.DataTable.isDataTable('#visitorsTable')) {
                    $('#visitorsTable').DataTable().draw(false);
                }
            },
            error: function (xhr) {
                Triggers.showToast(xhr.responseJSON?.message ?? 'Failed to delete visitor type.', 1);
            }
        });
    });





// /////////////////////////////////////////////////

});

class VisitorTypeTable {
    constructor() {
        this.defaultFields  = []
        // first parameter of your route
        this.url            = "/visitortype/"
        // id name of your table listing in user
        this.table          = "#visitorsTable"
        // module
        this.module         = "visitortype"
        // form id
        this.form           = "#textInputForm"
        // offCanvas
        this.modal          = "#textInputModal"
        // add user form id
        this.formid         = "#"  

    }


    async onLoadPage(){
        // this.initializePage();
        this.list();
    }

    // async initializePage(){
    //     const self = this

    //     //Open Modal
    //     $("#addBtn").on('click', function(){ 
    //         // console.log('clicked')   
    //         // Clear Form  
    //         datahandling.clearForm(self.form) 
    //         // $('#textInputModalLabel').text('Register Visitor Type');
    //         // show Canvas Form
    //         container.showModal(self.modal)

    //         // openTextInputModal('0', 'name')
    //     })

    //     $(this.form).on('submit', async function(e){
    //         e.preventDefault()
    //         await datahandling.saveForm('/visitortype/save', self.table, self.form, new FormData(this))
    //     })
        
    // }


    // async onLoadForm(record_id) {
    //     const self = this;

    //     const url = self.url+'search';
    //     const response = await datahandling.processData(
    //         url,
    //         'POST',
    //         { id: record_id }
    //     );

    //     $("#item_id").val(record_id);
    //     $("#requestItemName").val(response.record.name);

    //     $('#textInputModalLabel').text('Edit Visitor Type');

    //     container.showModal(self.modal);
    // }


    async list() {
        const self = this;

        const tableHeader = [
            { id: "name",        label: "Name" },
            { id: "created_by",  label: "Created By" },
            { id: "updated_by",  label: "Updated By" },
            { id: "created_at",  label: "Created Date" },
            { id: "action",      label: "Action" },
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
const visitorsType = new VisitorTypeTable();
visitorsType.onLoadPage();

