@extends('layout')

@section('content')

<div class="user-types-container mt-4">
    {{-- header --}}
    <div class="page-header report">
        <div class="header-content">
            <div class="page-title fs-2">Reports</div>
            <div class="page-subtitle mb-3">Track every logged visitor</div>
        </div>
        <div class="report-buttons d-flex gap-2 position-absolute top-50 end-0 translate-middle-y">
            <button id="exportReportBtn"
                class="top-button d-flex align-items-center justify-content-center
                text-white rounded-2 border-0 cursor-pointer px-3 py-2"
                title="Export to Excel">
                <i class="bi bi-download me-1"></i> Export 
            </button>
            <button id="openFilterBtn"
                class="top-button d-flex align-items-center justify-content-center
                text-white rounded-2 border-0 cursor-pointer px-3 py-2">
                <i class="bi bi-funnel me-1"></i> Filter Report
            </button>
        </div>
    </div>
    <!-- table holder -->
    <div class="visitor-log-sheet-table bg-white">
        <div class="bar">
            <div class="tab" id="visitor">
                Visitor
            </div>
            <div class="tab" id="employee"> 
                Employee
            </div>
        </div>
        <!-- search and filter -->
        <x-table-filter/>

        <!-- table -->
        <table class="table table-bordered align-middle" id="reportTable"><thead></thead></table>
    </div>
</div>
@include('components.triggers.users-userstype-toast')
@include('components.triggers.reportModals')

@endsection

@push('scripts')
@vite([
    'resources/js/report.js',
    'resources/js/logswitcher.js'
])
@endpush