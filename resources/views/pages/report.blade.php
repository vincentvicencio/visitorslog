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
@include('components.triggers.users-userstype-toast')
@include('components.triggers.reportModals')


<div class="user-types-container mt-4">
    <div class="page-header">
        <div class="header-content">
            <div class="page-title fs-2">Reports</div>
            <div class="page-subtitle mb-3">Monitor and track every logged visitor</div>
        </div>
        <div class="top-button position-absolute top-50 end-0 translate-middle-y d-flex align-items-center justify-content-center
         text-white rounded-2 border-0 cursor-pointer px-3 py-2" data-bs-toggle="modal" data-bs-target="#filterModal">
            <i class="bi bi-funnel me-1"></i> Filter Report
        </div>
        <!-- Filter Report -->
        <!-- <button class="top-button btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
            <i class="bi bi-funnel me-1"></i> Filter Report
        </button> -->
    </div>
    <!-- table.scss -->
    <div class="visitor-log-sheet-table table-responsive-sm table-responsive-md table-responsive-lg bg-white">
        <div class="search-field d-flex align-items-center justify-content-between w-100 rounded-3 mb-2 fw-bold fs-6 text-primary-custom ps-2 small-caps">
            search
            <input type="text" id="tableSearch" placeholder="search" class="flex-grow-1 mx-2 border-0 rounded-2 ms-2 me-4 ps-3">
       <!-- <input type="text" id="tableSearch" class="flex-grow-1 mx-2" placeholder="Search"> -->
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
                            <small><strong>Created:</strong> {{$reportlogs->getEmpName($reportlogs->created_by)}}</small><br>
                            <small><strong>Updated:</strong> {{$reportlogs->getEmpName($reportlogs->updated_by)}}</small>
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
                                    <button 
                                        class="dropdown-item"
                                        id="viewBtn"
                                        data-id="{{ $reportlogs->id }}"
                                        data-type="report">
                                        <i class="bi bi-eye me-2"></i> View
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item text-danger delete-btn" 
                                            data-id="{{ $reportlogs->id }}" 
                                            {{-- data-url="{{ route(name: 'visitors.destroy', $reportlogs->id) }}"--}}> 
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

@endsection

