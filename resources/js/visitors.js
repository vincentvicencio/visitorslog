import { Modal } from 'bootstrap';
import Triggers from './common/triggers.js';
import settable from './common/settable';
import $ from 'jquery';

const deleteModalEl = document.getElementById('notificationContainer');
const deleteModal = new Modal(deleteModalEl);
const video = document.getElementById('webcam');
const canvas = document.getElementById('canvas');
const captureBtn = document.getElementById('captureBtn');
const recaptureBtn = document.getElementById('recaptureBtn');
const photoPreview = document.getElementById('photoPreview');
const imageInput = document.getElementById('image_path');

let URL = '/visitorslog/';

// $(document).ready(function () {

    $('#addVisitorForm').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        
        // Disable submit button and show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Processing...');

        $.ajax({
            url: URL+"save",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            timeout: 30000,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Triggers.showToast(response.message, 0);
                setTimeout(() => {
                    $('.toast').fadeOut('slow');
                }, 2000);
                $('#addVisitorForm')[0].reset();
                photoPreview.src = "";
                photoPreview.style.display = 'none';
                video.style.display = 'block'; 
                imageInput.value = ""; 
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
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    $(document).on('click', '#clrBtn', function () {
        $('#addVisitorForm')[0].reset();
        photoPreview.src = "";
        photoPreview.style.display = 'none';
        video.style.display = 'block'; 
        imageInput.value = ""; 
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
            
            // Validate file size
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
            timeout: 15000, 
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
        Triggers.showNotification(
            '#notificationContainer',
            'Time Out',
            'Are you sure you want to time out this visitor?',
            Id
        );
    });

    $(document).on('click', '#timeout_btn', function () {
        let Id = $('#record_id').val();
        
        if (!Id) {
            Triggers.showToast('Invalid record ID.', 1);
            return;
        }
        $.ajax({
            url: URL+"timeout",
            type: "POST",
            data: {
                id: Id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            timeout: 15000,
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

            
            const appEl = document.getElementById('app');
            const isAdmin = appEl.dataset.type == 1;

            const tableHeader = [
                { id: "full_name", label: "Name" },
                ...(isAdmin ? [{ id: "location", label: "Location" }] : []),
                { id: 'contact_number', label: 'Contact No.' },
                { id: "visitor_type", label: "Visitor Type" },
                { id: "visitor_id", label: "ID No." },
                { id: "visit", label: "Visit Date" },
                { id: "time_in", label: "Time In" },
                { id: "time_out", label: "Time Out" },
                { id: "creator", label: "Logged by" },
                { id: "status", label: "Status" },
                { id: "action", label: "Action" },
            ];

            const columns = tableHeader.map(col => ({
                data: col.id,
                title: col.label,
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
            // =====================================================
            //  INIT COMPLETE (SAFE API ACCESS)
            // =====================================================

            $(self.table).on('init.dt', function () {

                const tableApi = $(self.table).DataTable();

                // START POLLING
                setInterval(() => {
                    tableApi.ajax.reload(null, false); 
                }, 5000); 

                // CUSTOM SEARCH
                $('#typeSearch')
                    .off('keyup')
                    .on('keyup', function () {
                        tableApi.search(this.value).draw();
                    });

                // ENTRIES PER PAGE
                $('#entriesPerPage')
                    .off('change')
                    .on('change', function () {
                        tableApi.page.len(this.value).draw();
                    });
            });
        }
    }

    const visitorsLog = new VisitorsLogTable();
    visitorsLog.onLoadPage();

    // 1. Start the webcam
    async function startWebcam() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: "user" }, 
                audio: false 
            });
            video.srcObject = stream;
        } catch (err) {
            console.error("Error accessing webcam: ", err);
            alert("Webcam access denied or not available.");
        }
    }

    // 2. Capture photo
    captureBtn.addEventListener('click', () => {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Draw the current video frame to the canvas
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Convert the canvas image to a data URL
        const imageData = canvas.toDataURL('image/png');    
        
        // Display the captured image
        photoPreview.src = imageData;
        photoPreview.style.display = 'block';
        video.style.display = 'none'; 
        imageInput.value = imageData; 
    });

    // 3. Recapture photo
    recaptureBtn.addEventListener('click', () => {
        photoPreview.src = "";
        photoPreview.style.display = 'none';
        video.style.display = 'block'; 
        imageInput.value = ""; 
    });

    startWebcam();
// });
