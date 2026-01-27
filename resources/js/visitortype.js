import Container from './common/container.js';
import Triggers from './common/triggers.js';
import Datahandling from './common/datahandling.js';
import { Modal } from 'bootstrap';

// Get modal element
const textInputModalEl = document.getElementById('textInputModal');
const textInputModal = new Modal(textInputModalEl);

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
    const id_number = input.dataset.id;
    const visitor_type = input.value.trim();
    if (!visitor_type) {
        Triggers.showToast('Visitor type cannot be empty.');
        return;
    }

    if(id === undefined) {
        // Send AJAX request to update visitor type
        $.ajax({
            url: "/visitorType/save",
            type: 'POST',
            data: {
                visitor_type: visitor_type,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Triggers.showToast(response.message);
                setTimeout(() => {
                    $(textInputModal.hide()).fadeOut('slow');
                }, 2000);
                setTimeout(() => {
                    $(location.reload()).fadeOut('slow');
                }, 2000);
            },
            error: function (xhr) {
                Triggers.showToast(xhr.responseJSON?.message ?? 'Save failed.');
                const input = document.getElementById('userInput');
                input.value = '';
            }
        });
    }else{
        // Send AJAX request to update visitor type
        $.ajax({
            url: "/visitorType/edit",
            type: 'POST',
            data: {
                id: id,
                visitor_type: visitor_type,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Triggers.showToast(response.message);
                // location.reload();
            },
            error: function (xhr) {
                Triggers.showToast(xhr.responseJSON?.message ?? 'Edit failed.');
                const input = document.getElementById('userInput');
                input.value = '';
            }
        });

        // Close modal
        setTimeout(() => {
            $(textInputModal.hide()).fadeOut('slow');
        }, 2000);
        setTimeout(() => {
            $(location.reload()).fadeOut('slow');
        }, 2000);
    }
    
});

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

    if (!id) return;

    if (!confirm('Are you sure you want to delete this Visitor Type?')) {
        return;
    }

    $.ajax({
        url: "visitorType/delete",
        type: "POST",
        data: {
            id: id,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            Triggers.showToast(response.message);
            setTimeout(() => {
                $('.toast').fadeOut('slow');
            }, 2000);
            setTimeout(() => {
                $(location.reload()).fadeOut('slow');
            }, 2000);
        },
        error: function (xhr) {
            Triggers.showToast(xhr.responseJSON?.message ?? 'Delete failed.');
        }
    });
});



