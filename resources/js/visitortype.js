import { Modal } from 'bootstrap';
import Triggers from './common/triggers.js';
import Container from './common/container.js';

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

// Handle submit
document.getElementById('textInputSubmit').addEventListener('click', () => {
    const input = document.getElementById('userInput');
    const id = input.dataset.id;
    const visitor_type = input.value.trim();
    if (!visitor_type) {
        Triggers.showToast('Please enter a visitor type name.', 1);
        return;
    }

    if(id === undefined) {
        // Send AJAX request to update visitor type
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
        // Send AJAX request to update visitor type
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
                // Close modal
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

    // Open modal when clicking edit
    $(document).on('click', '#editBtn', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');   // Make sure this is in your HTML
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
