@extends('layout')

@section('content')
<div class="user-types-container mt-4">
    <div class="page-header">
        <div class="header-content">
            <div class="page-title fs-2">Visitor Log Sheets</div>
            <div class="page-subtitle mb-3">Manage and track all visitor entries</div>
        </div>
        
        <div class="top-button" id="add_visitor">
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
        <x-table-pagination/>
    </div>
</div>

<!-- Add Request Item Modal -->
 <div class="addvisitor" style="display: none;">
    <div class="addvisitormodal">
        <div class="panel">
            <div class="header fs-4">Add Visitor</div>
            <div class="subheader mb-3">Register and record a new visitor entry</div>
            <button type="button" class="btn-close" aria-label="Close"></button>
            <div class="form">
                <div class="details">
                    <div class="input-holder floating">
                        <input type="text" name="id_number" id="" class="form-control" placeholder=" " >
                        <label for="id_number">ID Number</label>
                    </div>
                    <div class="input-holder floating">
                        <select name="visitor_type" id="visitor_type" class="form-control" required>
                            <option value="" disabled selected>Select Visitor Type</option> <!-- Empty option for floating effect -->
                            <option value="summit_one">Summit One</option>
                            <option value="facility_center">Facility Center</option>
                            <option value="mezzanine">Mezzanine</option>
                        </select>
                        <label for="visitor_type">Visitor Type</label>
                    </div>
                    <div class="input-holder floating">
                        <input type="text" name="id_number" id="" class="form-control" placeholder=" " >
                        <label for="id_number">first name</label>
                    </div>
                    <div class="input-holder floating">
                        <input type="text" name="id_number" id="" class="form-control" placeholder=" " >
                        <label for="id_number">middle name</label>
                    </div>
                    <div class="input-holder floating">
                        <input type="text" name="id_number" id="" class="form-control" placeholder=" " >
                        <label for="id_number">last name</label>
                    </div>
                    <div class="input-holder floating">
                        <input type="text" name="id_number" id="" class="form-control" placeholder=" " >
                        <label for="id_number">contact number</label>
                    </div>
                    <div class="input-holder floating w-100">
                        <textarea name="address" id="address" class="form-control" placeholder=" " rows="3"></textarea>
                        <label for="id_number">Address</label>
                    </div>
                </div>
                <div class="capture">
                    <div class="header">Capture Image</div>
                    <div class="imgholder">No Image</div>
                    <button type="button" class="capture-button">capture</button>
                </div>
            </div>
            <div class="panel-buttons">
                <button type="button" class="save">save</button>
                <button type="button" class="clear">clear</button>
            </div>
        </div>
    </div>
 </div>

@endsection
