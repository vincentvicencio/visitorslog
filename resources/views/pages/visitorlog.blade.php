@extends('layout')

@section('content')
<div class="user-types-container mt-4">
    <div class="page-header">
        <div class="header-content">
            <div class="page-title fs-2">Visitor Log Sheets</div>
            <div class="page-subtitle mb-3">Manage and track all visitor entries</div>
        </div>
        <div class="top-button">
            add visitor
        </div>
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
                    {{-- <td>{{ $visitor->visitor_type }}</td> --}}
                    <td>{{ $visitor->visitor_id }}</td>
                    <td>
                        <button class="btn-sm view-button">View</button>
                    </td>
                    {{-- <img src="{{ asset('storage/' . $visitor->image_path) }}" alt="Visitor Image" width="100"> --}}
                    <td>{{ $visitor->created_at->format('Y-m-d') }}</td>
                    <td>
                        <small><strong>In:</strong> {{ $visitor->time_in}} </small><br>
                        <small><strong>Out:</strong> {{ $visitor->time_out }}</small>
                    </td>
                    <td>
                        <small><strong>Created: </strong>{{ $visitor->created_by }}</small><br>
                        <small><strong>Updated: </strong>{{ $visitor->updated_by ?? '-' }}</small>
                    </td>
                    <td class="status-cell">
                        <div class="status">{{ $visitor->status == 1 ? 'Active' : 'Time Out' }}</div>
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
                                <form id="editForm" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="visitor_id" value="{{ $visitor->visitor_id }}">
                                    <li>
                                        <a class="dropdown-item" href="#" id="detailsBtn">
                                            <i class="bi bi-eye me-2"></i> View
                                        </a>
                                    </li>
                                    <li>
                                        <button class="dropdown-item text-danger" id="timeoutBtn">
                                            <i class="bi bi-clock-history me-2"></i> Timeout
                                        </button>
                                    </li>
                                </form>
                            </ul>
                        </div>
                    </td>
                    {{-- <td>
                        <form id="editForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="visitor_id" value="{{ $visitor->visitor_id }}">

                            <button type="button" id="detailsBtn" class="home-button">View Details</button>
                            <button type="button" id="timeoutBtn" class="timeout-button">Timeout</button>
                        </form>
                    </td> --}}
                    {{-- <a href="/visitors/{{ $visitor->id }}/edit">Edit</a> --}}
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
