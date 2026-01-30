@extends('layout')
@section('content')
<div class="user-types-container mt-4">
    <div class="page-header">
        <div class="header-content">
            <div class="page-title fs-2">Visitor Log Sheets</div>
            <div class="page-subtitle mb-3">Manage and track all visitor entries</div>
        </div>
        <a class="top-button position-absolute top-50 end-0 translate-middle-y d-flex align-items-center justify-content-center
            text-white rounded-2 border-0 cursor-pointer px-3 py-2 text-decoration-none" href="{{ route('visitor.index') }}" id="addBtn">
            Add Visitor
        </a>
    </div>
    <!-- table.scss -->
    <div class="visitor-log-sheet-table table-responsive-sm table-responsive-md table-responsive-lg bg-white">
        <div class="search-field d-flex align-items-center justify-content-between w-100 rounded-3 mb-2 fw-bold fs-6 text-primary-custom ps-2 small-caps">
            search
            <input type="text"id="typeSearch" placeholder="search" class="flex-grow-1 mx-2 border-0 rounded-2 ms-2 me-4 ps-3">
            entries per page
            <select name="" id="entriesPerPage" class="number-per-page">
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
            <tbody id="visitorLogTableBody">
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
                        @if($visitor->image_path == null)
                            No Image Provided
                        @else
                            <button 
                            class="btn-sm view-button text-white border-0 rounded-2 px-3 py-1"
                                id="viewImageBtn"
                                data-id="{{ $visitor->id }}"
                                data-image="{{ Storage::url($visitor->image_path) }}">
                                View
                            </button>
                        @endif
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
                        <div class="status rounded-2">{{ $visitor->status == 0 ? 'Active' : 'Time Out' }}</div>
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
                                    <button 
                                        class="dropdown-item"
                                        id="viewBtn"
                                        data-id="{{ $visitor->id }}">
                                        <i class="bi bi-eye me-2"></i> View
                                    </button>

                                </li>
                                <li>
                                    <button 
                                        type="button"
                                        class="dropdown-item text-danger"
                                        id="timeoutBtn"
                                        data-id="{{ $visitor->id }}">
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


@endsection
