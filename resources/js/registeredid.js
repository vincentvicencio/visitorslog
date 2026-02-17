import { Modal } from 'bootstrap';
import $ from 'jquery';
import Triggers from './common/triggers';
import settable from './common/settable';
import container from './common/container';
import datahandling from './common/datahandling';
import component from './common/component';

// //MODAL ELEMENT
// const textInputModalEl = document.getElementById('registerIDModal');
// const textInputModal = new Modal(textInputModalEl);
// const deleteModalEl = document.getElementById('notificationContainer');
// const deleteModal = new Modal(deleteModalEl);

// // PREFIX URL
// let URL = '/registerId/';

// // MODAL FUNCTION EDIT
// export function openTextInputModal(id, visitor_type, id_number) {
//     const input = document.getElementById('visitorID');
//     input.value = id_number;            // set current id_number
//     input.dataset.id = id; 
//     const visitorType = document.getElementById('visitortype');
//     visitorType.value = visitor_type;         // store id in data attribute       
//     textInputModal.show();
// }

// // MODAL FUNCTION ADD
// export function openTextInputModalBlank() {
//     const input = document.getElementById('visitorID');
//     const visitorType = document.getElementById('visitortype');
//     input.value = '';
//     visitorType.value = '';
//     textInputModal.show();
// }

// $(document).ready(function () {
//     $(document).on('click', '#addBtn', function () {
//         openTextInputModalBlank();
//     });
//     $(document).on('click', '#editBtn', function () {
//         const id = $(this).data('id');
//         const visitor_type = $(this).data('type'); 
//         const id_number = $(this).data('name');
//         if (!id) return;
//         openTextInputModal(id, visitor_type, id_number);
//     });

//     $(document).on('click', '#deleteBtn', function () {
//         let id = $(this).data('id');

//         Triggers.showNotification(
//             '#notificationContainer',
//             'Delete Register ID',
//             'Are you sure you want to delete this Visitor ID?',
//             id
//         );
//     });
        
//     $(document).on('click', '#btn_ok', function () {
//         let id = $('#record_id').val();
//         $.ajax({
//             url: URL+"delete",
//             type: "POST",
//             data: {
//                 id: id,
//                 _token: $('meta[name="csrf-token"]').attr('content')
//             },
//             success: function (response) {
//                 Triggers.showToast(response.message, 0);
//                 setTimeout(() => {
//                     $('.toast').fadeOut('slow');
//                     $(deleteModal.hide()).fadeOut('slow');
//                 }, 1000);
//                 if ($.fn.DataTable.isDataTable('#registerIdTable')) {
//                     $('#registerIdTable').DataTable().draw(false);
//                 }
//             },
//             error: function (xhr) {
//                 Triggers.showToast(xhr.responseJSON?.message, 1);
//             }
//         });
//     });

//     // SUBMIT
//     document.getElementById('registerIDSubmit').addEventListener('click', () => {
//         const visitor_id = document.getElementById('visitorID');
//         const id = visitor_id.dataset.id;
//         const visitorType = document.getElementById('visitortype');
//         const visitor_type = visitorType.value.trim();
//         const id_number = visitor_id.value.trim();
        
//         // Validate each field with specific messages
//         if (!visitor_type) {
//             Triggers.showToast('Please select a visitor type.', 1);
//             return;
//         }
        
//         if (!id_number) {
//             Triggers.showToast('Please enter a visitor ID number.', 1);
//             return;
//         }

//         if(id === undefined) {
//             // Send AJAX request to update visitor type
//             $.ajax({
//                 url: URL+"save",
//                 type: 'POST',
//                 data: {
//                     visitor_type: visitor_type,
//                     id_number: id_number,
//                     _token: $('meta[name="csrf-token"]').attr('content')
//                 },
//                 success: function (response) {
//                     Triggers.showToast(response.message, 0);
//                     setTimeout(() => {
//                         $(textInputModal.hide()).fadeOut('slow');
//                     }, 1000);
//                     if ($.fn.DataTable.isDataTable('#registerIdTable')) {
//                         $('#registerIdTable').DataTable().draw(false);
//                     }
//                 },
//                 error: function (xhr) {
//                     Triggers.showToast(xhr.responseJSON?.message ?? 'Save failed.', 1);
//                     const input = document.getElementById('visitorID');
//                     const visitorType = document.getElementById('visitortype');
//                     input.value = '';
//                     visitorType.value = '';
//                 }
//             });
//         }else{
//             // Send AJAX request to update visitor type
//             $.ajax({
//                 url: URL+"edit",
//                 type: 'POST',
//                 data: {
//                     id: id,
//                     visitor_type: visitor_type,
//                     id_number: id_number,
//                     _token: $('meta[name="csrf-token"]').attr('content')
//                 },
//                 success: function (response) {
//                     Triggers.showToast(response.message, 0);
//                     setTimeout(() => {
//                         $(textInputModal.hide()).fadeOut('slow');
//                     }, 1000);
//                     if ($.fn.DataTable.isDataTable('#registerIdTable')) {
//                         $('#registerIdTable').DataTable().draw(false);
//                     }
//                 },
//                 error: function (xhr) {
//                     Triggers.showToast(xhr.responseJSON?.message ?? 'Edit failed.', 1);
//                     const input = document.getElementById('visitorID');
//                     input.value = '';
//                 }
//             });
//         }
//     });

    // TABLE
    class RegisterIdTable {
        constructor() {
            this.defaultFields  = []
            // first parameter of your route
            this.url            = "/registerId/"
            // id name of your table listing in user
            this.table          = "#registerIdTable"
            // module
            this.module         = "registeredid"
            // form iddt-type-numeric
            this.form           = "#textInputForm"
            // offCanvas
            this.modal          = "#registerIDModal"
        }

    async initializePage(){
        this.list();
        this.initializeButtons();
    }
        async list() {
            const self = this;

            const tableHeader = [
                { id: "visitor_type",       label: "Name" },
                { id: "id_number",       label: "ID Number" },
                { id: "created_by",       label: "Created By" },
                { id: "updated_by",      label: "Updated By" },
                { id: "created_at",   label: "Created Date" },
                { id: "updated_at",   label: "Updated Date" },
                { id: "action",         label: "Action" },
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
            $('#addBtn').off('click').on('click', async function (e) {
                e.preventDefault()
                    datahandling.clearForm(self.form)
                    container.showModal(self.modal)
            })
            
            $(document).off('click', '#registerIDSubmit').on('click', '#registerIDSubmit', async function(e) {
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
                $("#name").val(response.data.id_number);
                $("#visitortype").val(response.data.visitor_type);
        
                container.showModal(self.modal);
        }

    }
    
    const instance = new RegisterIdTable();
    instance.initializePage();

    export default instance;




