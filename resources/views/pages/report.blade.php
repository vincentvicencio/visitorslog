@extends('layout')

@section('content')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    window.Laravel = {
        baseUrl: "{{ url('/') }}",
        csrfToken: "{{ csrf_token() }}" // This correctly gets the token from Laravel
    };
</script>

@vite(['resources/js/report.js'])


<div class="user-types-container mt-4">
    <div class="page-header">
        <div class="header-content">
            <div class="page-title fs-2">Reports</div>
            <div class="page-subtitle mb-3">Monitor and track every logged visitor</div>
        </div>
            <!-- Filter Report -->
            <button class="top-button btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
    <i class="bi bi-funnel me-1"></i> Filter Report
</button>
    </div>
    <!-- table.scss -->
    <div class="visitor-log-sheet-table table-responsive-sm table-responsive-md table-responsive-lg bg-white">
        <div class="search-field d-flex align-items-center justify-content-between">
            search
             <input type="text" id="tableSearch" class="flex-grow-1 mx-2" placeholder="Search">
            entries per page
            <select name="" id="entriesPerPage" class="number-per-page form-select-sm">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
        <!-- Table -->
        <table class="table table-bordered align-middle" id="visitorsTable">
            <thead>
                <tr class="table-header">
                    <th>Personal Details</th>
                    <th>Visitor Type</th>
                    <th>ID No.</th>
                    <th>Image</th>
                    <th>Visit</th>
                    <th>Time</th>
                    <th>By</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="reportTableBody">
                @foreach ($visitorlogs as $reportlogs)
                    <tr>
                        <td>
                            <strong>{{ "{$reportlogs->first_name} {$reportlogs->last_name}" }}</strong><br>
                            <small>{{ $reportlogs->phone_number }}</small><br>
                            <small>{{ $reportlogs->location_name }}</small>
                        </td>
                          @foreach ($visitorTypes as $type)
                        @if ($type->id == $reportlogs->visitor_type)
                            <td>{{ $type->name }}</td>
                        @endif
                    @endforeach
                        <td>{{ $reportlogs->visitor_id }}</td>
                        <td>
                            @if ($reportlogs->image_path)
                                <button type="button" class="btn-sm view-button btn btn-primary view-image-btn" 
                                data-image="{{ asset('storage/' . $reportlogs->image_path) }}">
                            View
                        </button>
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <td>{{ $reportlogs->created_at->format('F d, Y') }}</td>
                        <td>
                            <small><strong>In:</strong> {{ \Carbon\Carbon::parse($reportlogs->time_in)->format('h:i A') }}</small><br>
                            <small><strong>Out:</strong> 
                                @if ($reportlogs->time_out)
                                    {{ \Carbon\Carbon::parse($reportlogs->time_out)->format('h:i A') }}
                                @else
                                    N/A
                                @endif
                            </small>
                        </td>
                        <td>
                            <small><strong>Created:</strong> {{ $reportlogs->created_by }}</small><br>
                            <small><strong>Updated:</strong> {{ $reportlogs->updated_by }}</small>
                        </td>
                        <td class="status-cell">
                            @if ($reportlogs-> status == 0)
                                <div class="status">Active</div>
                            @else
                                <div class="status">InActive</div>
                            @endif
                            <!-- <div class="status">{{ $reportlogs->status }}</div> -->
                        </td>
                        <td class="text-center">
                            <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" 
                                    type="button" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-boundary="viewport" aria-expanded="false">
                                Action
                            </button>

                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-eye me-2"></i> View
                                    </a>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item text-danger delete-btn" 
                                            data-id="{{ $reportlogs->id }}" 
                                            data-url="{{ route('visitors.destroy', $reportlogs->id) }}">
                                        <i class="bi bi-trash me-2"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                            </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Pagination -->
        <x-table-pagination/>
    </div>
</div>


<!-- toast -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="deleteToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<!-- toast -->


<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Visitor Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Visitor Image">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Reports</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/report') }}" method="GET">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Visitor Type</label>
                        <select name="visitor_type" class="form-select">
                            <option value="">All Types</option>
                            @foreach ($visitorTypes as $type)
                                <option value="{{ $type->id }}" {{ request('visitor_type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ url('/report') }}" class="btn btn-secondary">Reset</a>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade text-center" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this visitor record? This action will move the log to the trash.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>

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
    // Add 'e' as a parameter to the function
    $('.view-image-btn').on('click', function(e) {
        // 1. Prevent the page from reloading
        e.preventDefault();
        
        // 2. Get the image URL from the data-image attribute
        const imageUrl = $(this).data('image');
        
        // 3. Set the src of the image inside the modal
        $('#modalImage').attr('src', imageUrl);
        
        // 4. Show the modal
        $('#imageModal').modal('show');
    });
    $('#imageModal').on('hidden.bs.modal', function () {
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

let visitorIdToDelete = null; // Renamed for clarity

// 1. When the "delete" button in the table is clicked
$(document).on('click', '.delete-btn', function() {
    visitorIdToDelete = $(this).data('id'); // Ensure your button has data-id="{{ $reportlogs->id }}"
    $('#deleteConfirmModal').modal('show');
});

// 2. When the "Delete Visitor" button inside the modal is clicked
$('#confirmDeleteBtn').on('click', function() {
    if (!visitorIdToDelete) return;

    const btn = $(this);
    btn.prop('disabled', true).text('Processing...');

    $.ajax({
        // Updated URL to /delete-visitor/ instead of /delete-user/
        url: window.Laravel.baseUrl + '/delete-visitor/' + visitorIdToDelete,
        type: "DELETE",
        data: {
            _token: window.Laravel.csrfToken
        },
        success: function(response) {
            $('#deleteConfirmModal').modal('hide');
            
            // Set toast message
            $('#toastMessage').text(response.success || "Visitor Deleted Successfully!");

            const toastElement = document.getElementById('deletesuccessToast');
            if (toastElement) {
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
            }

            // Reload page
            setTimeout(function() {
                location.reload();
            }, 1500); 
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert("Error deleting visitor record.");
            btn.prop('disabled', false).text('Delete Visitor');
            $('#deleteConfirmModal').modal('hide');
        }
    });
});
    

// // 1. Handle Delete Button Click
//     $('.delete-btn').on('click', function() {
//         const url = $(this).data('url');
//         $('#deleteForm').attr('action', url); // Set the form action to the specific ID
//         $('#deleteConfirmModal').modal('show');
//     });

//     // 2. Show Toast if session has success message
//     @if(session('success'))
//         const toastEl = document.getElementById('deleteToast');
//         const toast = new bootstrap.Toast(toastEl);
//         toast.show();
//     @endif

// $(document).on('click', '.delete-btn', function() {
//     const url = $(this).data('url');
//         $('#deleteForm').attr('action', url); // Set the form action to the specific ID
//         $('#deleteConfirmModal').modal('show');
// });

// // 2. When the "Delete" button inside the modal is clicked
// $('#confirmDeleteBtn').on('click', function() {
//     if (!userIdToDelete) return;

//     // Change button state to show processing
//     const btn = $(this);
//     btn.prop('disabled', true).text('Processing...');

//     $.ajax({
//         // url: "/delete-user/" + userIdToDelete,
//         url: window.Laravel.baseUrl + '/delete-user/' + userIdToDelete,
//         type: "POST",
//         data: {
//             _token: window.Laravel.csrfToken
//         },
//         success: function(response) {
//             // Success: Reload the page to refresh the table
//             $('#toastMessage').text(response.success || "User Deleted Successfully!");

//             // 2. Initialize and show the Bootstrap Toast
//             const toastElement = document.getElementById('deletesuccessToast');
//             const toast = new bootstrap.Toast(toastElement);
//             toast.show();

//             // 3. Optional: Delay the reload so the user can actually see the toast
//             setTimeout(function() {
//                 location.reload();
//             }, 1500); 
//         },
//         error: function(xhr) {
//             alert("Error deleting user.");
//             btn.prop('disabled', false).text('Delete');
//             $('#deleteConfirmModal').modal('hide');
//         }
//     });
// });

});
</script>
@endsection

