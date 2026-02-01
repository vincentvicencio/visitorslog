// $(document).ready(function() {
import { Modal } from 'bootstrap';
import Triggers from './common/triggers.js';


    $(document).on('shown.bs.dropdown', '.dropdown', function () {
    const $toggle = $(this).find('.dropdown-toggle');
    const $menu = $(this).find('.dropdown-menu');

    // Store the original parent so we can put it back later
    $menu.data('parent', $(this));
    
    $('body').append($menu);
    
    const offset = $toggle.offset();
    $menu.css({
        'display': 'block',
        'position': 'absolute',
        'visibility': 'visible',
        'opacity': '1',
        'top': offset.top + $toggle.outerHeight(),
        'left': offset.left,
        'z-index': '9999'
    }).addClass('show');
});

$(document).on('hide.bs.dropdown', '.dropdown', function () {
    const $menu = $('body > .dropdown-menu'); // Find the menu we moved to body
    const $parent = $menu.data('parent');
    
    if ($parent) {
        $parent.append($menu); // Put it back where it belongs
        $menu.css({
            'display': '',
            'position': '',
            'top': '',
            'left': ''
        }).removeClass('show');
    }
});

    // --- 1. GLOBAL VARIABLES --
    let currentPage = 1;

    // --- 2. INITIALIZATION ---
    function initTable() {
        $("#userTypeTableBody tr").addClass('search-match');
        applyPagination();
    }
    initTable();

    // --- 3. THE CORE PAGINATION FUNCTION ---
    function applyPagination() {
        const limit = parseInt($('#entriesPerPage').val()) || 10;
        const $allRows = $("#userTypeTableBody tr");
        
        // Filter rows that match the search criteria
        const $rowsToPaginate = $allRows.filter('.search-match');
        const totalRows = $rowsToPaginate.length;
        const totalPages = Math.ceil(totalRows / limit) || 1;

        // Boundary checks
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        // Hide all, then show only the current page slice
        $allRows.hide();
        const start = (currentPage - 1) * limit;
        const end = start + limit;
        $rowsToPaginate.slice(start, end).show();

        // Update pagination text
        $('.number-holder-pagination').text(`Page ${currentPage} of ${totalPages}`);

        updateArrowStyles(currentPage, totalPages);
    }

    function updateArrowStyles(curr, total) {
        const isFirst = curr === 1;
        const isLast = curr === total;
        $('.pagination-first, .pagination-prev').css({'opacity': isFirst ? '0.3' : '1', 'cursor': isFirst ? 'default' : 'pointer'});
        $('.pagination-next, .pagination-last').css({'opacity': isLast ? '0.3' : '1', 'cursor': isLast ? 'default' : 'pointer'});
    }

    // --- 4. EVENT LISTENERS ---

    // Search Logic
    $("#typeSearch").on("keyup", function() {
        const value = $(this).val().toLowerCase();
        $("#userTypeTableBody tr").each(function() {
            const rowText = $(this).text().toLowerCase();
            const isMatch = rowText.indexOf(value) > -1;
            $(this).toggleClass('search-match', isMatch);
        });
        currentPage = 1; 
        applyPagination(); 
    });

    // Entries Per Page Change
    $('#entriesPerPage').on('change', function() {
        currentPage = 1;
        applyPagination();
    });

    // Navigation Arrow Clicks
    $(document).on('click', '.pagination-first', function() { currentPage = 1; applyPagination(); });
    $(document).on('click', '.pagination-prev', function() { if(currentPage > 1) { currentPage--; applyPagination(); } });
    $(document).on('click', '.pagination-next', function() { 
        const limit = parseInt($('#entriesPerPage').val());
        const totalPages = Math.ceil($("#userTypeTableBody tr.search-match").length / limit);
        if(currentPage < totalPages) { currentPage++; applyPagination(); }
    });
    $(document).on('click', '.pagination-last', function() { 
        const limit = parseInt($('#entriesPerPage').val());
        const totalPages = Math.ceil($("#userTypeTableBody tr.search-match").length / limit);
        currentPage = totalPages; 
        applyPagination(); 
    });

    // --- 5. MODAL & AJAX OPERATIONS ---
  
  
  
//     $('#openAddTypePopup').click(function() { 
//         $('#save_type').text('Save New User Type');
//         $('#addTypeModal').fadeIn(200); }
//     );
//     $('#closeAddType').click(function() { $('#addTypeModal').fadeOut(200); });

//     // Handle Add Form
//     $('#add_type_form').on('submit', function(e) {
//         e.preventDefault();
//         const $btn = $(this).find('button[type="submit"]');
//         $btn.prop('disabled', true).text('Saving...');

//         $.ajax({
//             // url: "{{ route('addusertype') }}",
//             url:'/addusertype', 
//             type: "POST",
//             data: $(this).serialize(),
//             success: function(response) { 
                
//             $('#toastMessage').text(response.success || "User Type Added Successfully!");

//             // 2. Initialize and show the Bootstrap Toast
//             const toastElement = document.getElementById('SUCCESSTOAST');
//             const toast = new bootstrap.Toast(toastElement);
//             toast.show();

//             // 3. Optional: Delay the reload so the user can actually see the toast
//             setTimeout(function() {
//                 location.reload();
//             }, 1500); },
//             error: function(xhr) {
//                 $btn.prop('disabled', false).text('Save Role');
//                 alert("Error: " + (xhr.responseJSON.message || "Failed to add."));
//             }
//         });
//     });

//     // Open Edit Modal
//     $(document).on('click', '.edit-type', function() {
//         let id = $(this).data('id');
//         $('#save_type').text('Update');
//         $.get('/usertype/' + id + '/edit', function(data) {
//             $('#edit_type_id').val(data.id);
//             $('#edit_type_name').val(data.name);
//             $('#addTypeModal').fadeIn(200);
//         });
//     });

//     $('#closeEditType').click(function() { $('#addTypeModal').fadeOut(200); });
//     $('#edit_type_form').on('submit', function(e) {
//     e.preventDefault();
    
//     let id = $('#edit_type_id').val();
//     const $submitBtn = $(this).find('button[type="submit"]');
    
//     $submitBtn.prop('disabled', true).text('Updating...');

//     $.ajax({
//         url: '/usertype/' + id,
//         type: 'POST', // Use POST because we are using @method('PUT') in the form
//         data: $(this).serialize(),
//         success: function(response) {
//             // 1. Update the message text
//             $('#toastMessage').text(response.success || "User Type Updated Successfully!");

//             // 2. Initialize and show the Bootstrap Toast
//             const toastElement = document.getElementById('SUCCESSTOAST');
//             const toast = new bootstrap.Toast(toastElement);
//             toast.show();

//             // 3. Optional: Delay the reload so the user can actually see the toast
//             setTimeout(function() {
//                 location.reload();
//             }, 1500); 
//         },
//         error: function(xhr) {
//             $submitBtn.prop('disabled', false).text('Update Role');
//             alert("Error: " + (xhr.responseJSON.message || "Update failed."));
//         }
//     });
// });

// // Step C: Close Modal
// $('#closeEditType').click(function() { 
//     $('#addTypeModal').fadeOut(200); 
// });


// $('#openAddTypePopup').click(function() { 
//     const input = document.getElementById('edit_type_name');
//     input.value = '';
//     delete input.dataset.id;
    
//     $('#modalTitle').text('Add User Type');
//     $('#save_type').text('Save New User Type');
//     $('#addTypeModal').show(); 
// });

const usertypemodal = document.getElementById('addTypeModal');
const userTypeModal = new Modal(usertypemodal);

$('#openAddTypePopup').click(function() { 
    const input = document.getElementById('edit_type_name');
    input.value = '';
    delete input.dataset.id;
    
    $('#modalTitle').text('Add User Type');
    $('#save_type').text('Save New User Type');
    
    // Use Bootstrap's show() instead of jQuery's fadeIn()
    userTypeModal.show(); 
});

// $(document).on('click', '.edit-type', function() {
//     let id = $(this).data('id');
//     const input = document.getElementById('edit_type_name');
    
//     $('#save_type').text('Update');
//     $('#modalTitle').text('Edit User Type');

//     $.get('/usertype/' + id + '/edit', function(data) {
//         input.value = data.name;
//         input.dataset.id = data.id; 
//         $('#addTypeModal').fadeIn(200);
//     });
// });

$(document).on('click', '.edit-type', function() {
    let id = $(this).data('id');
    const input = document.getElementById('edit_type_name');
    
    $('#save_type').text('Update');
    $('#modalTitle').text('Edit User Type');

    $.get('/usertype/' + id + '/edit', function(data) {
        input.value = data.name;
        input.dataset.id = data.id; 
        
        // CHANGE THIS: Replace .fadeIn(200) with the Bootstrap show method
        userTypeModal.show();
    });
});

document.getElementById('save_type').addEventListener('click', () => {
    const input = document.getElementById('edit_type_name');
    const id = input.dataset.id;
    const user_type = input.value.trim();
    const $btn = $('#save_type');

    if (!user_type) {
        alert('User type name cannot be empty.');
        return;
    }

    $btn.prop('disabled', true).text(id === undefined ? 'Saving...' : 'Updating...');

    if (id === undefined) {
        $.ajax({
            url: "/addusertype",
            type: 'POST',
            data: {
                user_type: user_type,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                showSuccessFlow(response.success || "Added Successfully!");
            },
            error: function (xhr) {
                $btn.prop('disabled', false).text('Save New User Type');
                alert(xhr.responseJSON?.message ?? 'Save failed.');
            }
        });
    } else {
        $.ajax({
            url: '/usertype/' + id,
            type: 'POST',
            data: {
                id: id,
                user_type: user_type,
                _method: 'PUT',
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                showSuccessFlow(response.success || "Updated Successfully!");
            },
            error: function (xhr) {
                $btn.prop('disabled', false).text('Update');
                alert(xhr.responseJSON?.message ?? 'Edit failed.');
            }
        });
    }
});

function showSuccessFlow(message) {
    $('#toastMessage').text(message);
    const toastElement = document.getElementById('SUCCESSTOAST');
    const toast = new bootstrap.Toast(toastElement);
    toast.show();

    setTimeout(() => {
        $('#addTypeModal').fadeOut('slow');
        location.reload();
    }, 1500);
}

$('#closeAddType').click(function() { $('#addTypeModal').fadeOut(200); });

















    // let roleIdToDelete = null;

    // $(document).on('click', '.delete-type', function() {
    //     roleIdToDelete = $(this).data('id'); // Grab the ID
    //     $('#deleteRoleModal').modal('show');  // Open the Modal
    // });

    // $('#confirmDeleteRoleBtn').on('click', function() {
    //     if (!roleIdToDelete) return;

    //     const btn = $(this);
    //     btn.prop('disabled', true).text('Deleting...'); // Prevent double clicks

    //     $.ajax({
    //         url:'/usertype/' + roleIdToDelete,
    //         type: 'DELETE',
    //         data: { 
    //             _token: window.Laravel.csrfToken // CSRF token from Laravel
    //         },
    //         success: function(response) {
    //             $('#DeletetoastMessage').text(response.success || "User Type Deleted Successfully!");

    //         const toastElement = document.getElementById('DELETE');
    //         const toast = new bootstrap.Toast(toastElement);
    //         toast.show();

    //         setTimeout(function() {
    //             location.reload();
    //         }, 1500); 

    //         },
    //         error: function(xhr) {
    //             alert("Error deleting role.");
    //             btn.prop('disabled', false).text('Delete Role');
    //             $('#deleteRoleModal').modal('hide');
    //         }
    //     });
    // });






// Initialize the Modal instance using the correct ID from your HTML
const deleteModalEl = document.getElementById('notificationContainer');
const deleteModal = new Modal(deleteModalEl);

let roleIdToDelete = null;

// Open Delete Modal
$(document).on('click', '.delete-type', function() {
    roleIdToDelete = $(this).data('id'); // Grab the ID from the button
    
    // Update the notification text dynamically
    $('#notification-title').text('Confirm Deletion');
    $('#notification-message').text('Are you sure you want to delete this user type?');
    
    // Reset button state in case it was stuck on "Deleting..."
    $('#btn_ok').prop('disabled', false).text('Yes');

    // Show the modal
    deleteModal.show();
});

// Handle "Yes" Button Click
$('#btn_ok').on('click', function() {
    if (!roleIdToDelete) return;

    const btn = $(this);
    btn.prop('disabled', true).text('Deleting...');

    $.ajax({
        url: '/usertype/' + roleIdToDelete,
        type: 'DELETE',
        data: { 
            _token: window.Laravel.csrfToken 
        },
        success: function(response) {
            // Hide the confirmation modal
            deleteModal.hide();

            // Set message and fire the toast
            $('#DeletetoastMessage').text(response.success || "User Type Deleted Successfully!");
            const toastElement = document.getElementById('DELETE');
            
            if (toastElement) {
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
            }

            // Reload after toast
            setTimeout(function() {
                location.reload();
            }, 1500); 
        },
        error: function(xhr) {
            alert("Error deleting role: " + (xhr.responseJSON?.message || "Internal Server Error"));
            btn.prop('disabled', false).text('Yes');
            deleteModal.hide();
        }
    });
});









// });