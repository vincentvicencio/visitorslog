@extends('layout')

@section('content')

@vite(['resources/js/report.js'])
{{-- @vite('resources/js/ReportClass.js') --}}
@include('components.triggers.users-userstype-toast')
@include('components.triggers.reportModals')

<div class="user-types-container mt-4">
    <div class="page-header">
        <div class="header-content">
            <div class="page-title fs-2">Reports</div>
            <div class="page-subtitle mb-3">Monitor and track every logged visitor</div>
        </div>
        <div class="d-flex gap-4 position-absolute top-50 end-0 translate-middle-y">
            <button id="exportReportBtn"
                class="top-button d-flex align-items-center justify-content-center
                text-white rounded-2 border-0 cursor-pointer px-3 py-2"
                title="Export to Excel">
                <i class="bi bi-download me-1"></i> Export 
            </button>
            <div id="openFilterBtn"
                class="top-button position-relative d-flex align-items-center justify-content-center
                text-white rounded-2 border-0 cursor-pointer px-3 py-2">
                <i class="bi bi-funnel me-1"></i> Filter Report
            </div>
        </div>
    </div>
    <!-- table.scss -->
    <div class="visitor-log-sheet-table table-responsive-sm table-responsive-md table-responsive-lg bg-white">
        <!-- search and filter -->
        <x-table-filter/>

        <!-- table -->
        <table class="table table-bordered align-middle" id="reportTable">
            <thead></thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@endsection

