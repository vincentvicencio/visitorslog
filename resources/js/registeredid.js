import { Modal } from 'bootstrap';
import Triggers from './common/triggers.js';
import Container from './common/container.js';

// Get modal element
const textInputModalEl = document.getElementById('registerIDModal');
const textInputModal = new Modal(textInputModalEl);
const deleteModalEl = document.getElementById('notificationContainer');
const deleteModal = new Modal(deleteModalEl);

let URL = '/registerId/';

// Open modal function
export function openTextInputModal(id, visitor_type, id_number) {
    const input = document.getElementById('visitorID');
    input.value = id_number;            // set current id_number
    input.dataset.id = id; 
    const visitorType = document.getElementById('visitortype');
    visitorType.value = visitor_type;         // store id in data attribute       
    textInputModal.show();
}
export function openTextInputModalBlank() {
    // const input = document.getElementById('visitorID');
    // const visitorType = document.getElementById('visitortype');
    textInputModal.show();
}

// Handle submit
document.getElementById('registerIDSubmit').addEventListener('click', () => {
    const visitor_id = document.getElementById('visitorID');
    const id = visitor_id.dataset.id;
    const visitorType = document.getElementById('visitortype');
    const visitor_type = visitorType.value.trim();
    const id_number = visitor_id.value.trim();
    if (!visitor_id) {
        Triggers.showToast('Textfields cannot be empty.', 1);
        return;
    }

    if(id === undefined) {
        // Send AJAX request to update visitor type
        $.ajax({
            url: URL+"save",
            type: 'POST',
            data: {
                visitor_type: visitor_type,
                id_number: id_number,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Triggers.showToast(response.message, 0);
                setTimeout(() => {
                    $(textInputModal.hide()).fadeOut('slow');
                }, 1000);
                if ($.fn.DataTable.isDataTable('#registerIdTable')) {
                    $('#registerIdTable').DataTable().draw(false);
                }
            },
            error: function (xhr) {
                Triggers.showToast(xhr.responseJSON?.message ?? 'Save failed.', 1);
                const input = document.getElementById('visitorID');
                const visitorType = document.getElementById('visitortype');
                input.value = '';
                visitorType.value = '';
            }
        });
    }else{
        // Send AJAX request to update visitor type
        $.ajax({
            url: URL+"edit",
            type: 'POST',
            data: {
                id: id,
                visitor_type: visitor_type,
                id_number: id_number,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Triggers.showToast(response.message, 0);
                // Close modal
                setTimeout(() => {
                    $(textInputModal.hide()).fadeOut('slow');
                }, 1000);
                if ($.fn.DataTable.isDataTable('#registerIdTable')) {
                    $('#registerIdTable').DataTable().draw(false);
                }
            },
            error: function (xhr) {
                Triggers.showToast(xhr.responseJSON?.message ?? 'Edit failed.', 1);
                const input = document.getElementById('visitorID');
                input.value = '';
            }
        });
    }
    
});
$(document).ready(function () {
    $(document).on('click', '#addBtn', function () {
        openTextInputModalBlank();
    });

    // Open modal when clicking edit
    $(document).on('click', '#editBtn', function () {
        const id = $(this).data('id');
        const visitor_type = $(this).data('type'); 
        const id_number = $(this).data('name');
        if (!id) return;
        openTextInputModal(id, visitor_type, id_number);
    });



    $(document).on('click', '#deleteBtn', function () {
        let id = $(this).data('id');

        Triggers.showNotification(
            '#notificationContainer',
            'Delete Register ID',
            'Are you sure you want to delete this Visitor ID?',
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
                if ($.fn.DataTable.isDataTable('#registerIdTable')) {
                    $('#registerIdTable').DataTable().draw(false);
                }
            },
            error: function (xhr) {
                Triggers.showToast(xhr.responseJSON?.message, 1);
            }
        });;
    });
// /////////////////////////////////////////////////
});

