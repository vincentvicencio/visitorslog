@extends('layout')
@section('content')
@include('components.triggers.UsersModal')
@vite(['resources/js/users.js'])

<div class="user-types-container mt-4">
    <div class="page-header">
        <div class="header-content">
            <div class="page-title fs-2">Users</div>
            <div class="page-subtitle mb-3">Manage and organize user accounts and their details</div>
        </div>
        <div class="top-button position-absolute top-50 end-0 translate-middle-y d-flex align-items-center justify-content-center
         text-white rounded-2 border-0 cursor-pointer px-3 py-2" id="register_btn">
            Register User
        </div>
    </div>
    <!-- table.scss -->
    <div class="visitor-log-sheet-table table-responsive-sm table-responsive-md table-responsive-lg bg-white">
        <!-- search and filter -->
        <x-table-filter/>
    
        <table class="table table-bordered align-middle" id="usersTable">
            <thead></thead>
        </table>
    </div>
</div>

@endsection