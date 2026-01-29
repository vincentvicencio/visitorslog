@extends('layout')

@section('content')
<div class="user-types-container mt-4">
    <div class="page-header">
        <div class="header-content">
            <div class="page-title fs-2">Registered IDs</div>
            <div class="page-subtitle mb-3">Manage and monitor all registered ID entries</div>
        </div>
        <div class="top-button" id="addBtn">
            Register ID
        </div>
    </div>
    <!-- table.scss -->
    <div class="visitor-log-sheet-table table-responsive-sm table-responsive-md table-responsive-lg bg-white">
        <div class="search-field d-flex align-items-center justify-content-between">
            search
            <input type="text" id="typeSearch" placeholder="search" class="flex-grow-1 mx-2">
            entries per page
            <select name="" id="entriesPerPage" class="number-per-page">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
        <!-- Table -->
        <table class="table table-bordered align-middle" id="IDTable">
            <thead>
                <tr class="table-header">
                    <th>Name</th>
                    <th>ID Number</th>
                    <th>Created by</th>
                    <th>Updated by</th>
                    <th>Created date</th>
                    <th>Updated date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="visitorLogTableBody">
                 @forelse($registeredIds as $index => $visitor)
                    <tr>
                        @foreach ($visitorTypes as $type)
                            @if ($type->id == $visitor->visitor_type)
                                <td><strong>{{ $type->name }}</strong></td>
                            @endif
                        @endforeach
                        {{-- <td>{{ $visitor->visitor_type }}</td> --}}
                        <td>{{ $visitor->id_number }}</td>
                        <td>{{ $visitor->created_by }}</td>
                        <td>{{ $visitor->updated_by ?? '-' }}</td>
                        <td>
                            {{ $visitor->created_at->format('F d, Y') }}<br>
                            {{ $visitor->created_at->format('l') }}
                        </td> 
                        <td>
                            @if ($visitor->updated_at == $visitor->created_at)
                               -
                            @else
                                {{ $visitor->updated_at->format('F d, Y') }}<br>
                                {{ $visitor->updated_at->format('l') }}
                            @endif
                        </td>
                        <td class="text-center">
                            @if (!$visitorsLogs->contains('visitor_id', $visitor->id_number))
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
                                                id="editBtn"
                                                data-id="{{ $visitor->id }}"
                                                data-type="{{ $visitor->visitor_type }}"
                                                data-name="{{ $visitor->id_number }}"
                                                >
                                                <i class="bi bi-pencil-square me-2"></i> Edit
                                            </button>

                                        </li>
                                        <li>
                                            <button 
                                                type="button"
                                                class="dropdown-item text-danger"
                                                id="deleteBtn"
                                                data-id="{{ $visitor->id }}">
                                                <i class="bi bi-trash me-2"></i> Delete
                                            </button>

                                        </li>
                                    </ul>
                                </div>
                            @else
                               
                                <button 
                                    type="button"
                                    class="dropdown-item text-success">
                                    Currently Used
                                </button>
                            @endif
                            
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;">No visitor IDs registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Pagination -->
        <x-table-pagination/>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@vite('resources/js/registeredid.js')
@include('components.triggers.registerIdModal')


@endsection
