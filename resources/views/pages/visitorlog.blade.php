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
            <tbody>
                <tr>
                    <td>
                        <strong>Juan Dela Cruz</strong><br>
                        <small>0917-123-4567</small><br>
                        <small>Quezon City</small>
                    </td>
                    <td>Summit One</td>
                    <td>1023</td>
                    <td>
                        <button class="btn-sm view-button">View</button>
                    </td>
                    <td>January 22, 2026 <br>Thursday</td>
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

                <tr>
                    <td>
                        <strong>Maria Santos</strong><br>
                        <small>0920-555-8899</small><br>
                        <small>Makati City</small>
                    </td>
                    <td>Facility Center</td>
                    <td>4578</td>
                    <td>
                        <button class="btn-sm view-button">View</button>
                    </td>
                    <td>January 21, 2026 <br>Wednesday</td>
                    <td>
                        <small><strong>In:</strong> 09:00 AM</small><br>
                        <small><strong>Out:</strong> —</small>
                    </td>
                    <td>
                        <small><strong>Created:</strong> Security</small><br>
                        <small><strong>Updated:</strong> </small>
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

                <tr>
                    <td>
                        <strong>Pedro Ramirez</strong><br>
                        <small>0918-777-3322</small><br>
                        <small>Pasig City</small>
                    </td>
                    <td>Mezzanine</td>
                    <td>8891</td>
                    <td>
                        <button class="btn-sm view-button">View</button>
                    </td>
                    <td>January 20, 2026 <br>Tuesday</td>
                    <td>
                        <small><strong>In:</strong> 10:15 AM</small><br>
                        <small><strong>Out:</strong> 11:05 AM</small>
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
            </tbody>
        </table>
        <!-- Pagination -->
        <div class="d-flex gap-2 align-items-center table-pagination">
            <div class="arrow-holder-pagination">
                <div class="bi bi-chevron-double-left" id="visitor-log-sheet-latest"></div> <!-- << -->
                <div class="bi bi-chevron-left" id="visitor-log-sheet-previous"></div>        <!-- < -->
            </div>
            <div class="number-holder-pagination">Page 1 of 10</div>
            <div class="arrow-holder-pagination">
                <div class="bi bi-chevron-right" id="visitor-log-sheet-next"></div>       <!-- > -->
                <div class="bi bi-chevron-double-right" id="visitor-log-sheet-last"></div> <!-- >> -->
            </div>
        </div>
    </div>
</div>

@endsection
