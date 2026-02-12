@extends('layout')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@push('scripts')
@vite (['resources/js/usertype.js'])
@include('components.triggers.users-userstype-toast')
@include('components.triggers.UserTypeModal')
@endpush

<div class="user-types-container mt-4">
    {{-- header --}}
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
        <!-- search and filter - calling table filter componentq -->
        <x-table-filter/>
        <!-- table -->
        <table class="table table-bordered align-middle" id="userTypeTable"><thead></thead></table>
    </div>
</div>
@endsection