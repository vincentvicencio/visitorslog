@extends('layout')

@section('content')
<<<<<<< HEAD
=======
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
>>>>>>> b9f5eab92d97d014f501ef89784c529eecffa38f

@vite(['resources/js/report.js'])
@include('components.triggers.users-userstype-toast')
@include('components.triggers.reportModals')

<div class="user-types-container mt-4">
    {{-- header --}}
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
    <div class="visitor-log-sheet-table table-responsive-sm table-responsive-md table-responsive-lg bg-white">
        <!-- search and filter - calling table filter component -->
        <x-table-filter/>
        <!-- table -->
<<<<<<< HEAD
        <table class="table table-bordered align-middle" id="reportTable">
            <thead></thead>
            <tbody></tbody>
        </table>
=======
        <table class="table table-bordered align-middle" id="reportTable"><thead></thead></table>
>>>>>>> b9f5eab92d97d014f501ef89784c529eecffa38f
    </div>
</div>

@endsection

