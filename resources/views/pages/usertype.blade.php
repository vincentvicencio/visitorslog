@extends('layout')

@section('content')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@push('scripts')
@vite(['resources/js/userTypeClass.js'])
@vite (['resources/js/usertype.js'])
@include('components.triggers.users-userstype-toast')
@include('components.triggers.UserTypeModal')
@endpush

<script>
    window.Laravel = {
        baseUrl: "{{ url('/') }}",
        csrfToken: "{{ csrf_token() }}" // This correctly gets the token from Laravel
    };
</script>


<div class="user-types-container mt-4">
    <div class="page-header">
        <div class="header-content">
            <div class="page-title fs-2">User Types</div>
            <div class="page-subtitle mb-3">Manage and organize different user roles</div>
        </div>
        <div class="top-button position-absolute top-50 end-0 translate-middle-y d-flex align-items-center justify-content-center
        text-white rounded-2 border-0 cursor-pointer px-3 py-2" id="openAddTypePopup">
            Add User Type
        </div>
    </div>

    <div class="visitor-log-sheet-table table-responsive-sm table-responsive-md table-responsive-lg bg-white">
        <div class="search-field d-flex align-items-center justify-content-between w-100 rounded-3 mb-2 fw-bold fs-6 text-primary-custom ps-2 small-caps">
            search
            <input type="text"id="usertypesearch" placeholder="search" class="flex-grow-1 mx-2 border-0 rounded-2 ms-2 me-4 ps-3">
            entries per page
            <select name="" id="entriesPerPage" class="number-per-page">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select> 
        </div>
            <table class="table table-bordered align-middle" id="userTypeTable">
                <thead></thead>
            </table>
        </div>
        </div>
    </div>
</div>
@endsection