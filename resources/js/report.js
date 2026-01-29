$(document).ready(function(){

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
    // Add 'e' as a parameter to the function
    $('.view-image-btn').on('click', function(e) {
        // 1. Prevent the page from reloading
        e.preventDefault();
        
        // 2. Get the image URL from the data-image attribute
        const imageUrl = $(this).data('image');
        
        // 3. Set the src of the image inside the modal
        $('#modalImage').attr('src', imageUrl);
        
        // 4. Show the modal
        $('#View_imageModal').modal('show');
    });
    $('#View_imageModal').on('hidden.bs.modal', function () {
        $('#modalImage').attr('src', ''); 
    });


    function updateTableRows() {
        var limit = parseInt($('#entriesPerPage').val()); 
        var $rows = $('#reportTableBody tr');
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
        var $rows = $("#reportTableBody tr");

        if (value === "") {
            updateTableRows(); 
        } else {
            $rows.filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        }
    });

 // --- 1. GLOBAL VARIABLES ---
    let currentPage = 1;

    // --- 2. INITIALIZATION ---
    // Mark all rows as matches initially so pagination shows them all
    $("#reportTableBody tr").addClass('search-match');
    applyPagination();

    // --- 3. SEARCH LOGIC ---
    $("#tableSearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        var $rows = $("#reportTableBody tr");

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
        const $allRows = $("#reportTableBody tr");
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
        const totalPages = Math.ceil($("#reportTableBody tr.search-match").length / limit);
        if (currentPage < totalPages) {
            currentPage++;
            applyPagination();
        }
    });

    $(document).on('click', '.pagination-last', function() {
        const limit = parseInt($('#entriesPerPage').val());
        const totalPages = Math.ceil($("#reportTableBody tr.search-match").length / limit);
        if (currentPage < totalPages) {
            currentPage = totalPages;
            applyPagination();
        }
    });


    let visitorIdToDelete = null; 

    // 1. When the "delete" button in the table is clicked
    $(document).on('click', '.delete-btn', function() {
        // This grabs the ID from the specific button you clicked
        visitorIdToDelete = $(this).data('id'); 
        $('#deleteConfirmModal').modal('show');
    });

    // 2. The AJAX call
    $('#confirmDeleteBtn').on('click', function() {
        if (!visitorIdToDelete) return;

        const btn = $(this);
        btn.prop('disabled', true).text('Processing...');

        $.ajax({
            url: window.Laravel.baseUrl + '/delete-visitor/' + visitorIdToDelete,
            type: "DELETE",
            data: {
                _token: window.Laravel.csrfToken
            },
            success: function(response) {
            //   Success: Reload the page to refresh the table
            $('#toastMessage').text(response.success || "User Deleted Successfully!");

            // 2. Initialize and show the Bootstrap Toast
            const toastElement = document.getElementById('delete_report_successToast');
            const toast = new bootstrap.Toast(toastElement);
            toast.show();

            // 3. Optional: Delay the reload so the user can actually see the toast
            setTimeout(function() {
                location.reload();
            }, 1500); 
            },
            error: function(xhr, status, error) {
                console.error(error);
                btn.prop('disabled', false).text('Delete');
            }   
        });
    });

});

