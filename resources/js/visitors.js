import { Modal } from 'bootstrap';
import Triggers from './common/triggers.js';
import Datatable from './common/settable.js';
import Container from './common/container.js';

$(document).ready(function () {

    $('#addVisitorForm').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "/visitor/save",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Triggers.showToast(response.message, 0);
                
                setTimeout(() => {
                    window.location.href = "/visitorlog";
                }, 2000);
            },
            error: function (xhr) {
                let msg = xhr.responseJSON?.message ?? 'Save failed.';
                Triggers.showToast(msg, 1);
            }
        });
    });

    $(document).on('click', '#clrBtn', function () {
        $('#addVisitorForm')[0].reset();
    });

    $('#captureBtn').on('click', function () {
        $('#imageInput').click();
    });

    $(document).on('click', '#viewBtn', function () {
        let visitorId = $(this).data('id');
            let type = $(this).data('type');

        if (!visitorId) return;

        $.ajax({
            url: "/visitor/view",
            type: "POST",
            data: {
                id: visitorId,
                type: type,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                // ✅ redirect after AJAX success
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

    const imageModal = new Modal(document.getElementById('imageModal'));

    $(document).on('click', '#viewImageBtn', function () {
        const imageUrl = $(this).data('image');

        $('#modalImage').attr('src', imageUrl);
        imageModal.show();
    });


    // ////////////////////////////////////////////////////////////////////////////////////

//     const video = document.getElementById('webcam');
// const canvas = document.getElementById('canvas');
// const captureBtn = document.getElementById('captureBtn');
// const photoPreview = document.getElementById('photoPreview');
// const imageInput = document.getElementById('image_data');

// // 1. Start the Webcam automatically on page load
// async function startWebcam() {
//     try {
//         const stream = await navigator.mediaDevices.getUserMedia({ 
//             video: { facingMode: "user" }, // "user" for front, "environment" for back
//             audio: false 
//         });
//         video.srcObject = stream;
//     } catch (err) {
//         console.error("Error accessing webcam: ", err);
//         alert("Webcam access denied or not available.");
//     }
// }

// // 2. Capture the frame
// captureBtn.addEventListener('click', () => {
//     const context = canvas.getContext('2d');
    
//     // Set canvas size to match video dimensions
//     canvas.width = video.videoWidth;
//     canvas.height = video.videoHeight;
    
//     // Draw the current video frame onto the canvas
//     context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
//     // Convert canvas to a Base64 URL (image string)
//     const imageData = canvas.toDataURL('image/png');
    
//     // Show preview and store data in the hidden input
//     photoPreview.src = imageData;
//     photoPreview.style.display = 'block';
//     video.style.display = 'none'; // Hide video once captured
//     imageInput.value = imageData; 
    
//     console.log("Image captured successfully!");
// });

// startWebcam();



    
});



//     // Edit
//     async onLoadForm(record_id) {

//         const self = this;

//         const url = ${self.url}search;
//         const users = await datahandling.processData(url, 'POST',  { id: record_id })

//         $("#user_id").val(record_id);
//         $("#empCode").val(users.records.emp_code)
//         $("#empName").val(users.employee_details.emp_name)
//         $("#userType").val(users.records.user_type).trigger('change');
        
//         component.createDropdown(
//                 '/registered_user/get_user_types', 
//                 '#userType', 
//                 null, 
//                 self.modal 
//             );

//         $('#searchUser').closest('.mb-3').hide();
//         $('#addUserModalLabel').text('Edit User');

//         Container.showModal(self.modal)
//     }