$(document).ready(function() {
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': window.Laravel.csrfToken,
        'X-Requested-With': 'XMLHttpRequest'
    }
});
    // Handle Dropdown placement without breaking the click event
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
    function updateTableRows() {
        var limit = parseInt($('#entriesPerPage').val()); 
        var $rows = $('#employeeTableBody tr');
        $rows.hide();
        $rows.slice(0, limit).show();

        console.log("Showing " + limit + " rows");
    }
    updateTableRows();

    $('#entriesPerPage').on('change', function() {
        updateTableRows();
    });

    $("#tableSearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        var $rows = $("#employeeTableBody tr");

        if (value === "") {
            updateTableRows(); 
        } else {
            $rows.filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        }
    });

    $('#openPopup2').click(function() { $('#registeruserpopup').fadeIn(); 

        // component.createDropdown('/get-locations', '#reg_location', null, null1);
        // Check if component exists before calling
    // if (typeof component !== 'undefined') {
        component.createDropdown('/getlocation', '#reg_location', null, '#registeruserpopup');
    // } else {
    //     console.error("The 'component' object is not defined. Check if its JS file is loaded.");
    // }
    });
$('#closePopup2').click(function() { $('#registeruserpopup').fadeOut(); });

// Handle AJAX Submission
$('#registered_user_form').on('submit', function(e) {
    e.preventDefault(); 

    $.ajax({
        // url: "{{ route('addusers') }}", 
        url: window.Laravel.baseUrl + '/addusers', 
        type: "POST",
        data: {
                _token: window.Laravel.csrfToken,
                emp_code: $('#reg_emp_code').val(),
                password: $('#reg_password').val(),
                user_type: $('#reg_user_type').val(), // This will now grab the ID from the select above
                locations: $('#reg_location').val(), // New location field
            },
        success: function(response) {
           $('#toastMessage').text(response.success || "User Added Successfully!");

            // 2. Initialize and show the Bootstrap Toast
            const toastElement = document.getElementById('add_user_successToast');
            const toast = new bootstrap.Toast(toastElement);
            toast.show();

            // 3. Optional: Delay the reload so the user can actually see the toast
            setTimeout(function() {
                location.reload();
            }, 1000); 
        },
        error: function(xhr) {
            // This will now show you the specific validation error from Laravel
            console.log(xhr.responseText);
            alert("Validation Error: " + xhr.responseJSON.message);
        }
    });
});

function copyToRegister(code) {
    // Fill the employee code input in your existing registration form
    $('#reg_emp_code').val(code);
    
    // Open the registration popup (registeruserpopup)
    $('#registeruserpopup').fadeIn();
}
// --- DELETE USER LOGIC ---
let userIdToDelete = null;

// 1. When the delete button in the table is clicked
$(document).on('click', '.delete-user', function() {
    userIdToDelete = $(this).data('id'); // Store the ID
    $('#deleteConfirmModal').modal('show'); // Show the modal
});

// 2. When the "Delete" button inside the modal is clicked
$('#confirmDeleteBtn').on('click', function() {
    if (!userIdToDelete) return;

    // Change button state to show processing
    const btn = $(this);
    btn.prop('disabled', true).text('Processing...');

    $.ajax({
        // url: "/delete-user/" + userIdToDelete,
        url: window.Laravel.baseUrl + '/delete-user/' + userIdToDelete,
        type: "POST",
        data: {
            _token: window.Laravel.csrfToken
        },
        success: function(response) {
            // Success: Reload the page to refresh the table
            $('#toastMessage').text(response.success || "User Deleted Successfully!");

            // 2. Initialize and show the Bootstrap Toast
            const toastElement = document.getElementById('delete_user_successToast');
            const toast = new bootstrap.Toast(toastElement);
            toast.show();

            // 3. Optional: Delay the reload so the user can actually see the toast
            setTimeout(function() {
                location.reload();
            }, 1500); 
        },
        error: function(xhr) {
            alert("Error deleting user.");
            btn.prop('disabled', false).text('Delete');
            $('#deleteConfirmModal').modal('hide');
        }
    });
});


$(document).on('click', '.edit-user', function() {
    var userId = $(this).data('id');
    
    // CHANGE THIS: Add the baseUrl variable to the GET request
    $.get(window.Laravel.baseUrl + "/get-user/" + userId, function(data) {
        $('#edit_user_id').val(data.id);
        $('#edit_user_type').val(data.role_id); 
        $('#edit_emp_code').val(data.emp_code);
        $('#editPopupContainer').fadeIn();
    }).fail(function(xhr) {
        // If this triggers, it means the fetch itself is getting the 401 error
        console.error("Fetch failed: ", xhr.status);
    });
});

$('#closeEditPopup').click(function() { $('#editPopupContainer').hide(); });

$('#edit_user_form').on('submit', function(e) {
    e.preventDefault();
    var userId = $('#edit_user_id').val(); 

    $.ajax({
        url: window.Laravel.baseUrl + '/update-user/' + userId,
        type: "POST",
        data: $(this).serialize(), 
        success: function(response) {
            $('#editPopupContainer').fadeOut(200);
            $('#toastMessage').text(response.message || "User updated successfully!");

            const toastElement = document.getElementById('edit_user_successToast');
            const toast = new bootstrap.Toast(toastElement);
            toast.show();

            setTimeout(function() {
                location.reload();
            }, 1500);
        },
        error: function(xhr) {
            if (xhr.status === 401) {
                alert("Your session has expired. Please refresh and log in again.");
            } else {
                alert("Error: " + (xhr.responseJSON ? xhr.responseJSON.message : "Update failed"));
            }
        }
    });
});
    // --- 1. GLOBAL VARIABLES ---
    let currentPage = 1;

    // --- 2. INITIALIZATION ---
    // Mark all rows as matches initially so pagination shows them all
    $("#employeeTableBody tr").addClass('search-match');
    applyPagination();

    // --- 3. SEARCH LOGIC ---
    $("#tableSearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        var $rows = $("#employeeTableBody tr");

        $rows.each(function() {
            var rowText = $(this).text().toLowerCase();
            // Check if row is the "No results" row or if it matches search
            var isMatch = rowText.indexOf(value) > -1;
            
            if (isMatch) {
                $(this).addClass('search-match');
            } else {
                $(this).removeClass('search-match');
            }
        });

        currentPage = 1; // Reset to first page on new search
        applyPagination(); 
    });

    // --- 4. PAGINATION CORE FUNCTION ---
    function applyPagination() {
        const limit = parseInt($('#entriesPerPage').val()) || 10;
        const $allRows = $("#employeeTableBody tr");
        const $rowsToPaginate = $allRows.filter('.search-match');

        const totalRows = $rowsToPaginate.length;
        const totalPages = Math.ceil(totalRows / limit) || 1;

        // Boundary checks
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        // Hide all rows, then show only the current slice
        $allRows.hide();
        const start = (currentPage - 1) * limit;
        const end = start + limit;
        $rowsToPaginate.slice(start, end).show();

        // Update the custom pagination UI text
        $('.number-holder-pagination').text(`Page ${currentPage} of ${totalPages}`);

        // Visual feedback for arrows (opacity and cursor)
        updateArrowStyles(currentPage, totalPages);
    }

    function updateArrowStyles(curr, total) {
        const isFirst = curr === 1;
        const isLast = curr === total;

        $('.pagination-first, .pagination-prev').css({
            'opacity': isFirst ? '0.3' : '1',
            'cursor': isFirst ? 'default' : 'pointer'
        });
        $('.pagination-next, .pagination-last').css({
            'opacity': isLast ? '0.3' : '1',
            'cursor': isLast ? 'default' : 'pointer'
        });
    }

    // --- 5. EVENT LISTENERS ---

    // Entries Per Page Change
    $('#entriesPerPage').on('change', function() {
        currentPage = 1;
        applyPagination();
    });

    // Arrow Click Events
    $(document).on('click', '.pagination-first', function() {
        if (currentPage > 1) {
            currentPage = 1;
            applyPagination();
        }
    });

    $(document).on('click', '.pagination-prev', function() {
        if (currentPage > 1) {
            currentPage--;
            applyPagination();
        }
    });

    $(document).on('click', '.pagination-next', function() {
        const limit = parseInt($('#entriesPerPage').val());
        const totalPages = Math.ceil($("#employeeTableBody tr.search-match").length / limit);
        if (currentPage < totalPages) {
            currentPage++;
            applyPagination();
        }
    });

    $(document).on('click', '.pagination-last', function() {
        const limit = parseInt($('#entriesPerPage').val());
        const totalPages = Math.ceil($("#employeeTableBody tr.search-match").length / limit);
        if (currentPage < totalPages) {
            currentPage = totalPages;
            applyPagination();
        }
    });

    // --- 6. EXISTING MODAL & AJAX LOGIC ---
    // (Keep your Register, Edit, and Delete AJAX code here...)
    $('#openPopup2').click(function() { $('#registeruserpopup').fadeIn(); });
    $('#closePopup2').click(function() { $('#registeruserpopup').fadeOut(); });

    // Handle Dropdown placement (if needed for table scrolling)
    $(document).on('shown.bs.dropdown', '.dropdown', function () {
        const $menu = $(this).find('.dropdown-menu');
        $('body').append($menu);
        const offset = $(this).offset();
        $menu.css({
            'display': 'block',
            'top': offset.top + $(this).outerHeight(),
            'left': offset.left
        });
    });

    $(document).on('hide.bs.dropdown', '.dropdown', function () {
        $('body > .dropdown-menu').remove();
    });
});