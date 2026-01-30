import { Modal } from 'bootstrap';
import Triggers from './common/triggers.js';
import Container from './common/container.js';

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
        Triggers.showToast('Visitor type cannot be empty.', 1);
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
                Triggers.showToast(response.message, 0);
                setTimeout(() => {
                    $(textInputModal.hide()).fadeOut('slow');
                }, 2000);
                setTimeout(() => {
                    $(location.reload()).fadeOut('slow');
                }, 2000);
            },
            error: function (xhr) {
                Triggers.showToast(xhr.responseJSON?.message ?? 'Save failed.', 1);
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
                Triggers.showToast(response.message, 0);
                // Close modal
                setTimeout(() => {
                    $(textInputModal.hide()).fadeOut('slow');
                }, 2000);
                setTimeout(() => {
                    $(location.reload()).fadeOut('slow');
                }, 2000);

            },
            error: function (xhr) {
                Triggers.showToast(xhr.responseJSON?.message ?? 'Edit failed.', 1);
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
            url: "visitorType/delete",
            type: "POST",
            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Triggers.showToast(response.message, 0);
                setTimeout(() => {
                    $('.toast').fadeOut('slow');
                }, 2000);
                setTimeout(() => {
                    $(location.reload()).fadeOut('slow');
                }, 2000);
            },
            error: function (xhr) {
                Triggers.showToast(xhr.responseJSON?.message ?? 'Delete failed.', 1);
            }
        });
    });





// /////////////////////////////////////////////////


    // ================= PAGINATION =================
    let currentPage = 1;

    function initTable() {
        $("#visitorLogTableBody tr").addClass('search-match');
        applyPagination();
    }

    function applyPagination() {
        const limit = parseInt($('#entriesPerPage').val()) || 10;
        const $rows = $("#visitorLogTableBody tr.search-match");
        const totalRows = $rows.length;
        const totalPages = Math.ceil(totalRows / limit) || 1;

        currentPage = Math.min(Math.max(currentPage, 1), totalPages);

        $("#visitorLogTableBody tr").hide();

        const start = (currentPage - 1) * limit;
        $rows.slice(start, start + limit).show();

        $('.number-holder-pagination')
            .text(`Page ${currentPage} of ${totalPages}`);

        updateArrowStyles(currentPage, totalPages);
    }

    function updateArrowStyles(curr, total) {
        $('.pagination-first, .pagination-prev')
            .css({ opacity: curr === 1 ? 0.3 : 1 });

        $('.pagination-next, .pagination-last')
            .css({ opacity: curr === total ? 0.3 : 1 });
    }

    // ================= SEARCH =================
    $("#typeSearch").on("keyup", function () {
        const value = $(this).val().toLowerCase();

        $("#visitorLogTableBody tr").each(function () {
            $(this).toggleClass(
                'search-match',
                $(this).text().toLowerCase().includes(value)
            );
        });

        currentPage = 1;
        applyPagination();
    });

    // ================= CONTROLS =================
    $('#entriesPerPage').on('change', () => {
        currentPage = 1;
        applyPagination();
    });

    $(document).on('click', '.pagination-first', () => {
        currentPage = 1; applyPagination();
    });

    $(document).on('click', '.pagination-prev', () => {
        if (currentPage > 1) currentPage--;
        applyPagination();
    });

    $(document).on('click', '.pagination-next', () => {
        currentPage++; applyPagination();
    });

    $(document).on('click', '.pagination-last', () => {
        const limit = parseInt($('#entriesPerPage').val()) || 10;
        currentPage = Math.ceil(
            $("#visitorLogTableBody tr.search-match").length / limit
        );
        applyPagination();
    });

    initTable();
});
