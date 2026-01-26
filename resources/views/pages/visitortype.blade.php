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
            background-color: #fff;
            padding: 10px;
            border-radius: 8px;
            max-width: 90%;
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

        /* Button to open modal */
        .openModalBtn {
            padding: 8px 16px;
            background-color: #007bff;
            border: none;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }

        .openModalBtn:hover {
            background-color: #0056b3;
        }

    </style>
@section('content')
<div class="user-types-container mt-4">
    <div class="page-header">
        <div class="header-content">
            <div class="page-title fs-2">Visitor Type</div>
            <div class="page-subtitle mb-3">Manage and organize different visitor categories</div>
        </div>
        {{-- <div class="top-button">
            Add Visitor Type
        </div> --}}
         <form action="/visitor_type" method="post">
            @csrf
            <button class="top-button" type="submit">
                Add Visitor Type
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
        <table class="table table-bordered align-middle" id="visitortypeTable">
            <thead>
                <tr class="table-header">
                    <th>Name</th>
                    <th>Created by</th>
                    <th>Updated by</th>
                    <th>Created date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                {{-- <tr>
                    <td><strong>Juan_DC</strong></td>
                    <td>Receptionist</td>
                    <td>-</td>
                    <td>January 22, 2026 <br> Thursday, 8:30 AM</td>



                </tr> --}}

                @forelse($visitorTypes as $index => $visitor)
                    <tr>
                        <td><strong>{{ $visitor->name }}</strong></td>
                        <td>{{ $visitor->created_by }}</td>
                        <td>{{ $visitor->updated_by ?? '-' }}</td>
                        <td>
                            {{ $visitor->created_at->format('F d, Y') }}<br>
                            {{ $visitor->created_at->format('l') }}
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

                                {{-- <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('visitorType.edit', ['id' => $visitor->id]) }}">
                                            <i class="bi bi-pencil-square me-2"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="{{ route('visitorType.delete', ['id' => $visitor->id]) }}">
                                            <i class="bi bi-trash me-2"></i> Delete
                                        </a>
                                    </li>
                                </ul> --}}
                                <ul class="dropdown-menu">
                                    <li>
                                        <button 
                                            class="dropdown-item editBtn"
                                            data-id="{{ $visitor->id }}">
                                            <i class="bi bi-pencil-square me-2"></i> Edit
                                        </button>

                                    </li>
                                    <li>
                                        <button 
                                            type="button"
                                            class="dropdown-item text-danger deleteBtn"
                                            data-id="{{ $visitor->id }}">
                                            <i class="bi bi-trash me-2"></i> Delete
                                        </button>

                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;">No visitor types added yet.</td>
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
        <img src="path/to/image.jpg" alt="Modal Image" class="modal-image">   
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).on('click', '.deleteBtn', function () {
    let id = $(this).data('id');

    if (!id) return;

    if (!confirm('Are you sure you want to delete this Visitor Type?')) {
        return;
    }

    $.ajax({
        url: "{{ route('visitorType.delete.ajax') }}",
        type: "POST",
        data: {
            id: id,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            alert(response.message);
            location.reload();
        },
        error: function (xhr) {
            alert(xhr.responseJSON?.message ?? 'Delete failed.');
        }
    });
});

document.querySelectorAll('.editBtn').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        if (id) {
            // alert('Edit functionality is not implemented yet.');
            document.getElementById('simpleModal').style.display = 'block';
            
        }
    });
});

document.querySelectorAll('.closeBtn').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('simpleModal').style.display = 'none';
    });
});

</script>


@endsection
