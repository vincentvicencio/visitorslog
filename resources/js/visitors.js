import { Modal } from 'bootstrap';
import Triggers from './common/triggers.js';
import settable from './common/settable';
import $ from 'jquery';

const deleteModalEl = document.getElementById('notificationContainer');
const deleteModal = new Modal(deleteModalEl);

let URL = '/visitorslog/';

$(document).ready(function () {

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
                Triggers.showToast(response.message, 'Success', 0);
                setTimeout(() => {
                    $('.toast').fadeOut('slow');
                }, 2000);
                $('#addVisitorForm')[0].reset();
                $('#image_path').val(''); 
                $('#photoPreview').css('display', 'none');
                $('#photoPreview').attr('src', '');
            },
            error: function (xhr, status, error) {
                console.error('Save error:', error, xhr);
                let msg = 'Save failed.';
                let title = 'Error';
                if (status === 'timeout') {
                    msg = 'Request timeout. Please check your connection.';
                } else if (xhr.responseJSON?.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.status === 422) {
                    msg = 'Validation error. Please check your input.';
                } else if (xhr.status >= 500) {
                    msg = 'Server error. Please try again later.';
                }
                
                Triggers.showToast(msg, title, 1);
            },
            complete: function () {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    $(document).on('click', '#clrBtn', function () {
        $('#addVisitorForm')[0].reset();
        $('#photoPreview').css('display', 'none');
        $('#photoPreview').attr('src', '');
        $('#imageInput').val(''); 
    });

    $('#captureBtn').on('click', function () {
        $('#imageInput').click();
    });

    $('#recaptureBtn').on('click', function () {
        $('#photoPreview').css('display', 'none');
        $('#photoPreview').attr('src', '');
        $('#imageInput').val(''); 

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


    $('#imageInput').on('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                Triggers.showToast('Invalid file type. Please upload an image.', 'Invalid', 1);
                return;
            }
            
            // Validate file size
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                Triggers.showToast('File size exceeds 5MB limit.', 'Size Limit Exceeded', 1);
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function (event) {
                $('#photoPreview').css('display', 'block');
                $('#photoPreview').attr('src', event.target.result);

            };
            reader.onerror = function (error) {
                console.error('FileReader error:', error);
                Triggers.showToast('Failed to read image file.', 'Error', 1);
            };
            reader.onabort = function () {
                Triggers.showToast('Image read was cancelled.', 'Cancelled', 1);
            };
            reader.readAsDataURL(file);
        }
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
            Triggers.showToast('Invalid record ID.', 'Invalid', 1);
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
                Triggers.showToast(response.message, 'Success', 0);
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
                
                Triggers.showToast(msg, 'Error', 1);
            }
        });
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

            const appEl = document.getElementById('usertypeCheck');
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
            //  DEBUG: LOG AJAX RESPONSE
            // =====================================================
            $(self.table).on('xhr.dt', function (e, settings, json) {
                console.log(' AJAX RESPONSE:', json);
            });

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
    // captureBtn.addEventListener('click', () => {
    //     const context = canvas.getContext('2d');
    //     // Set canvas size to match video dimensions
    //     canvas.width = video.videoWidth;
    //     canvas.height = video.videoHeight;
        
    //     // Draw the current video frame to the canvas
    //     context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
    //     // Convert the canvas image to a data URL
    //     const imageData = canvas.toDataURL('image/png');    
        
    //     // Display the captured image
    //     photoPreview.src = imageData;
    //     photoPreview.style.display = 'block';
    //     video.style.display = 'none'; 
    //     imageInput.value = imageData; 
    // });

    recaptureBtn.addEventListener('click', () => {
        photoPreview.src = "";
        photoPreview.style.display = 'none';
        video.style.display = 'block'; // Hide video once captured
        imageInput.value = ""; 
    });
    startWebcam();
});
