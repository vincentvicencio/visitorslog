@extends('layout')
     <style>
        /* Modal overlay */
        .modal {
            display: none; /* hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8); /* dark translucent background */
            justify-content: center;
            align-items: center;
        }

        /* Modal content box */
        .modal-content {
            position: relative;
            background-color: none;
            padding: 10px;
            border-radius: 8px;
            max-width: 50%;
            max-height: 90%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        /* Image inside modal */
        .modal-image {
            max-width: 100%;
            max-height: 80vh;
            border-radius: 6px;
        }

        /* Close button */
        .closeBtn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 28px;
            font-weight: bold;
            color: #333;
            cursor: pointer;
            transition: color 0.2s;
        }

        .closeBtn:hover {
            color: #000;
        }
    </style>
@section('content')
<div class="user-types-container mt-4">
    <div class="page-header">
        <div class="header-content">
            <div class="page-title fs-2">Visitor Log Sheets</div>
            <div class="page-subtitle mb-3">Manage and track all visitor entries</div>
        </div>
        
        <form action="/visitor" method="post">
            @csrf
            <button class="top-button" type="submit">
                Add Visitor
            </button>
            
        </form>
    </div>
    <!-- table.scss -->
    <div class="visitor-log-sheet-table table-responsive-sm table-responsive-md table-responsive-lg bg-white">
        <div class="search-field d-flex align-items-center justify-content-between">
            search
            <input type="text" placeholder="search" class="flex-grow-1 mx-2">
            entries per page
            <select name="" id="" class="number-per-page">
                <option value="">10</option>
                <option value="">25</option>
                <option value="">50</option>
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
            {{-- <tbody>
                <tr>
                    <td>
                        <small><strong>In:</strong> 08:30 AM</small><br>
                        <small><strong>Out:</strong> 09:45 AM</small>
                    </td>
                    <td>
                        <small><strong>Created:</strong> Admin</small><br>
                        <small><strong>Updated:</strong> Admin</small>
                    </td>
                    <td class="status-cell">
                        <div class="status">Active</div>
                    </td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button 
                                class="btn btn-sm btn-primary dropdown-toggle"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Action
                            </button>

                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-eye me-2"></i> View
                                    </a>
                                </li>
                                <li>
                                    <button class="dropdown-item text-danger">
                                        <i class="bi bi-clock-history me-2"></i> Timeout
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </td>



                </tr>
            </tbody> --}}
            <tbody>
                @forelse($visitors as $index => $visitor)
                <tr>
                    <td>
                        <strong>{{ $visitor->first_name }} {{ $visitor->middle_name }} {{ $visitor->last_name }}</strong>
                        @if ($visitor->location == 1)
                            <br><small>Facility Center</small>
                        @elseif ($visitor->location == 2)
                            <br><small>Summit One</small>
                        @else
                            <br><small>Mezzanine</small>
                        @endif
                        
                        <br><small>{{ $visitor->phone_number }}</small>    
                    </td>
                    @foreach ($visitorTypes as $type)
                        @if ($type->id == $visitor->visitor_type)
                            <td>{{ $type->name }}</td>
                        @endif
                    @endforeach
                    <td>{{ $visitor->visitor_id }}</td>
                    <td>
                        <button class="btn-sm view-button viewImageBtn" data-image="{{ Storage::url($visitor->image_path) }}">View</button>

                    </td>
                    <td>
                        {{ $visitor->created_at->format('F d, Y') }}<br>
                        {{ $visitor->created_at->format('l') }}
                    </td>

                    <td>
                        <small><strong>In:</strong> {{ \Carbon\Carbon::parse($visitor->time_in)->format('h:i A') }}</small><br>
                        <small>
                            <strong>Out:</strong>
                            {{ $visitor->time_out ? \Carbon\Carbon::parse($visitor->time_out)->format('h:i A') : '-' }}
                        </small>
                    </td>
                    <td>
                        <small><strong>Created: </strong>{{ $visitor->created_by }}</small><br>
                        <small><strong>Updated: </strong>{{ $visitor->updated_by ?? '-' }}</small>
                    </td>
                    <td class="status-cell">
                        <div class="status">{{ $visitor->status == 0 ? 'Active' : 'Time Out' }}</div>
                    </td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button 
                                class="btn btn-sm btn-primary dropdown-toggle"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Action
                            </button>

                            <ul class="dropdown-menu">
                                <li>
                                    {{-- <a class="dropdown-item" href="{{ route('visitor.view', ['visitor_id' => $visitor->visitor_id]) }}">
                                        <i class="bi bi-eye me-2"></i> View
                                    </a> --}}
                                    <button 
                                        class="dropdown-item viewBtn"
                                        data-id="{{ $visitor->visitor_id }}">
                                        <i class="bi bi-eye me-2"></i> View
                                    </button>

                                </li>
                                <li>
                                    {{-- <a class="dropdown-item text-danger" href="{{ route('visitor.timeout', ['visitor_id' => $visitor->visitor_id]) }}">
                                        <i class="bi bi-clock-history me-2"></i> Timeout
                                    </a> --}}
                                    <button 
                                        type="button"
                                        class="dropdown-item text-danger timeoutBtn"
                                        data-id="{{ $visitor->visitor_id }}">
                                        <i class="bi bi-clock-history me-2"></i> Timeout
                                    </button>

                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;">No visitors registered yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Pagination -->
        <x-table-pagination/>
    </div>
</div>

<div id="simpleModal" class="modal">
    <div class="modal-content">
        <span class="closeBtn">&times;</span>
        <img src="" alt="Modal Image" class="modal-image">   
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> --}}




<script>
    $(document).on('click', '.timeoutBtn', function () {
        let visitorId = $(this).data('id');

        if (!visitorId) return;

        if (!confirm('Are you sure you want to time out this visitor?')) {
            return;
        }

        $.ajax({
            url: "{{ route('visitor.timeout.ajax') }}",
            type: "POST",
            data: {
                visitor_id: visitorId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                alert(response.message);

                // Option 1: reload page
                location.reload();

                // Option 2 (later): update row status dynamically
            },
            error: function (xhr) {
                alert('Something went wrong. Please try again.');
            }
        });
    });
    $(document).on('click', '.viewBtn', function () {
        let visitorId = $(this).data('id');

        if (!visitorId) return;

        $.ajax({
            url: "{{ route('visitor.view') }}",
            type: "POST",
            data: {
                visitor_id: visitorId,
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
                alert(msg);
            }
        });
    });

    document.querySelectorAll('.viewImageBtn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-image');
            if (id) {
                // alert('Edit functionality is not implemented yet.');
                document.getElementById('simpleModal').style.display = 'flex';
                document.querySelector('.modal-image').src = id;
                
            }
        });
    });

    document.querySelectorAll('.closeBtn', '.modal').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('simpleModal').style.display = 'none';
        });
    });
    document.querySelectorAll('.modal').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('simpleModal').style.display = 'none';
        });
    });


</script>

@endsection
