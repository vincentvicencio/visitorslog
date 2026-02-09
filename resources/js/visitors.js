import { Modal } from 'bootstrap';
import Triggers from './common/triggers.js';
import container from './common/container';
import datahandling from './common/datahandling';
import settable from './common/settable';
import component from './common/component';
import $ from 'jquery';

const deleteModalEl = document.getElementById('notificationContainer');
const deleteModal = new Modal(deleteModalEl);

let URL = '/visitorslog/';

$(document).ready(function () {

    


    $('#addVisitorForm').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        
        // Disable submit button to prevent duplicate submissions
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Processing...');

        $.ajax({
            url: URL+"save",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            timeout: 30000, // 30 second timeout
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Triggers.showToast(response.message, 0);
                setTimeout(() => {
                    $('.toast').fadeOut('slow');
                }, 1000);
            $('#addVisitorForm')[0].reset();
            $('.imgholder').html('Image');
            },
            error: function (xhr, status, error) {
                console.error('Save error:', error, xhr);
                let msg = 'Save failed.';
                
                if (status === 'timeout') {
                    msg = 'Request timeout. Please check your connection.';
                } else if (xhr.responseJSON?.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.status === 422) {
                    msg = 'Validation error. Please check your input.';
                } else if (xhr.status >= 500) {
                    msg = 'Server error. Please try again later.';
                }
                
                Triggers.showToast(msg, 1);
            },
            complete: function () {
                // Re-enable submit button
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    $(document).on('click', '#clrBtn', function () {
        $('#addVisitorForm')[0].reset();
        $('.imgholder').html('Image');
    });

    $('#captureBtn').on('click', function () {
        $('#imageInput').click();
    });

    $('#imageInput').on('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                Triggers.showToast('Invalid file type. Please upload an image.', 1);
                return;
            }
            
            // Validate file size (max 5MB)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                Triggers.showToast('File size exceeds 5MB limit.', 1);
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function (event) {
                $('.imgholder').html(`<img src="${event.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`);
            };
            reader.onerror = function (error) {
                console.error('FileReader error:', error);
                Triggers.showToast('Failed to read image file.', 1);
            };
            reader.onabort = function () {
                Triggers.showToast('Image read was cancelled.', 1);
            };
            reader.readAsDataURL(file);
        }
    });

    $(document).on('click', '#viewBtn', function () {
        let visitorId = $(this).data('id');
        let type = $(this).data('type');

        if (!visitorId) {
            Triggers.showToast('Invalid visitor ID.', 1);
            return;
        }

        $.ajax({
            url: URL+"view",
            type: "POST",
            data: {
                id: visitorId,
                type: type,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            timeout: 15000, // 15 second timeout
            success: function (response) {
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    Triggers.showToast('No redirect URL provided.', 1);
                }
            },
            error: function (xhr, status, error) {
                console.error('View error:', error, xhr);
                let msg = 'Unable to load visitor details.';
                
                if (status === 'timeout') {
                    msg = 'Request timeout. Please try again.';
                } else if (xhr.responseJSON?.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    msg = 'Visitor not found.';
                } else if (xhr.status >= 500) {
                    msg = 'Server error. Please try again later.';
                }
                
                Triggers.showToast(msg, 1);
            }
        });
    });

    $(document).on('click', '#timeoutBtn', function () {
        let Id = $(this).data('id');
        console.log('tanga')
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
        
        if (!Id) {
            Triggers.showToast('Invalid record ID.', 1);
            return;
        }
        console.log('tanga');
        $.ajax({
            url: URL+"timeout",
            type: "POST",
            data: {
                id: Id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            timeout: 15000, // 15 second timeout
            success: function (response) {
                Triggers.showToast(response.message, 0);
                setTimeout(() => {
                    $('.toast').fadeOut('slow');
                    try {
                        deleteModal.hide();
                    } catch (e) {
                        console.error('Modal hide error:', e);
                    }
                }, 1000);
                if ($.fn.DataTable.isDataTable('#visitorsLogTable')) {
                    $('#visitorsLogTable').DataTable().draw(false);
                }
            },
            error: function (xhr, status, error) {
                console.error('Timeout error:', error, xhr);
                let msg = 'TimeOut failed.';
                
                if (status === 'timeout') {
                    msg = 'Request timeout. Please try again.';
                } else if (xhr.responseJSON?.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    msg = 'Record not found.';
                } else if (xhr.status >= 500) {
                    msg = 'Server error. Please try again later.';
                }
                
                Triggers.showToast(msg, 1);
            }
        });
    });
    // /////////////////////////////////////////////////

    let imageModal;
    try {
        const imageModalEl = document.getElementById('imageModal');
        if (imageModalEl) {
            imageModal = new Modal(imageModalEl);
        } else {
            console.warn('Image modal element not found');
        }
    } catch (e) {
        console.error('Error initializing image modal:', e);
    }

    $(document).on('click', '#viewImageBtn', function () {
        const imageUrl = $(this).data('image');

        if (!imageUrl) {
            Triggers.showToast('Image URL not found.', 1);
            return;
        }
        
        if (imageModal) {
            $('#modalImage').attr('src', imageUrl);
            try {
                imageModal.show();
            } catch (e) {
                console.error('Error showing image modal:', e);
                Triggers.showToast('Failed to display image.', 1);
            }
        } else {
            Triggers.showToast('Image modal not available.', 1);
        }
    });

console.log('🔥 visitors.js file loaded');

class VisitorsLogTable {
    constructor() {
        this.defaultFields  = []
        this.url            = "/visitorslog/"
        this.table          = "#visitorsLogTable"
        this.module         = "visitorslog"
        this.form           = "#"
        this.modal          = "#"
        this.formid         = "#"  
    }

    async onLoadPage(){
        this.list();
    }

    async list() {
        const self = this;

        const tableHeader = [
            { id: "full_name",    label: "Personal Details" },
            { id: "visitor_type", label: "Visitor Type" },
            { id: "visitor_id",   label: "ID No." },
            { id: "image",        label: "Image" },
            { id: "visit",        label: "Visit" },
            { id: "time",         label: "Time" },
            { id: "creator",      label: "By" },
            { id: "status",       label: "Status" },
            { id: "action",       label: "Action" },
        ];

        const columns = tableHeader.map(col => ({
            data: col.id,
            title: col.label
        }));

        const columnDefs = [
            { targets: [0, 1, 2, 3], orderable: false }
        ];

        console.log('🚀 BEFORE createTableAjax');

        // ✅ ADD `{ dom: 'rtip' }` to REMOVE default search input
        settable.createTableAjax(
            self.table,
            columns,
            self.url,
            columnDefs,
            10,
            {},
            false // 🔥 THIS REMOVES THE DEFAULT SEARCH BAR
        );

        console.log('🚀 AFTER createTableAjax');

        // =====================================================
        // 🧪 DEBUG: LOG AJAX RESPONSE
        // =====================================================
        $(self.table).on('xhr.dt', function (e, settings, json) {
            console.log('✅ AJAX RESPONSE:', json);
        });

        // =====================================================
        // ✅ INIT COMPLETE (SAFE API ACCESS)
        // =====================================================
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

/* =====================================================
   ✅ THIS WAS MISSING (CRITICAL)
   ===================================================== */
const visitorsLog = new VisitorsLogTable();
visitorsLog.onLoadPage();


    // ////////////////////////////////////////////////////////////////////////////////////

    const video = document.getElementById('webcam');
const canvas = document.getElementById('canvas');
const captureBtn = document.getElementById('captureBtn');
const recaptureBtn = document.getElementById('recaptureBtn');
const photoPreview = document.getElementById('photoPreview');
const imageInput = document.getElementById('image_path');

// 1. Start the Webcam automatically on page load
async function startWebcam() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: "user" }, // "user" for front, "environment" for back
            audio: false 
        });
        video.srcObject = stream;
    } catch (err) {
        console.error("Error accessing webcam: ", err);
        alert("Webcam access denied or not available.");
    }
}

// 2. Capture the frame
captureBtn.addEventListener('click', () => {
    const context = canvas.getContext('2d');
    console.log(context)
    // Set canvas size to match video dimensions
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw the current video frame onto the canvas
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Convert canvas to a Base64 URL (image string)
    const imageData = canvas.toDataURL('image/png');    
    console.log(imageData)
    // Show preview and store data in the hidden input
    photoPreview.src = imageData;
    photoPreview.style.display = 'block';
    video.style.display = 'none'; // Hide video once captured
    imageInput.value = imageData; 
    
    // console.log();
});

recaptureBtn.addEventListener('click', () => {
    photoPreview.src = "";
    photoPreview.style.display = 'none';
    video.style.display = 'block'; // Hide video once captured
    imageInput.value = ""; 
    
    console.log("Image captured successfully!");
});


startWebcam();



    
});