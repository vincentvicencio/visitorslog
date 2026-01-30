import { Modal } from 'bootstrap';
import Triggers from './common/triggers.js';
import Container from './common/container.js';

$(document).ready(function () {

    $(document).on('click', '#timeoutBtn', function () {
        let Id = $(this).data('id');

        // if (!Id) return;
        Triggers.showNotification(
            '#notificationContainer',
            'Time Out',
            'Are you sure you want to time out this visitor?',
            Id
        );
    });

    $(document).on('click', '#btn_ok', function () {
        let Id = $('#record_id').val();
        $.ajax({
            url: "/visitor/timeout",
            type: "POST",
            data: {
                id: Id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Triggers.showToast(response.message, 0);

                setTimeout(() => {
                    location.reload(); // ✅ correct reload
                }, 2000);
            },
            error: function (xhr) {
                console.log(xhr); // 👈 helpful for debugging

                let msg = xhr.responseJSON?.message ?? 'TimeOut failed.';
                Triggers.showToast(msg, 1);
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


    $(document).on('click', '#addBtn', function () {

        Container.showModal('#addVisitorModal');
    });

    $(document).on('click', '#addBtn', function () {

        Container.showModal('#addVisitorModal');
    });

    $(document).on('click', '#addBtn', function () {

        Container.showModal('#addVisitorModal');
    });
});



const imageModal = new Modal(document.getElementById('imageModal'));

$(document).on('click', '#viewImageBtn', function () {
    const imageUrl = $(this).data('image');

    $('#modalImage').attr('src', imageUrl);
    imageModal.show();
});





