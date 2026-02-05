import { Modal } from 'bootstrap';
import Triggers from './common/triggers.js';
import Datatable from './common/settable.js';
import Container from './common/container.js';

const deleteModalEl = document.getElementById('notificationContainer');
const deleteModal = new Modal(deleteModalEl);

let URL = '/visitorslog/';

$(document).ready(function () {

    $('#addVisitorForm').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: URL+"save",
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
                    $('.toast').fadeOut('slow');
                }, 1000);
                setTimeout(() => {
                    window.location.href = "/visitorlog";
                }, 1000);
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
            url: URL+"view",
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
            url: URL+"timeout",
            type: "POST",
            data: {
                id: Id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Triggers.showToast(response.message, 0);
                setTimeout(() => {
                    $('.toast').fadeOut('slow');
                    $(deleteModal.hide()).fadeOut('slow');
                }, 1000);
                if ($.fn.DataTable.isDataTable('#visitorsLogTable')) {
                    $('#visitorsLogTable').DataTable().draw(false);
                }
            },
            error: function (xhr) {
                console.log(xhr); // 👈 helpful for debugging

                let msg = xhr.responseJSON?.message ?? 'TimeOut failed.';
                Triggers.showToast(msg, 1);
            }
        });
    });
    // /////////////////////////////////////////////////

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