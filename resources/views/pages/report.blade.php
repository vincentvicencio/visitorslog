@extends('layout')

@section('content')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    window.Laravel = {
        baseUrl: "{{ url('/') }}",
        csrfToken: "{{ csrf_token() }}" // This correctly gets the token from Laravel
    };
</script>

@vite(['resources/js/report.js'])
@include('components.triggers.users-userstype-toast')
@include('components.triggers.reportModals')
@vite('resources/js/ReportClass.js')


<div class="user-types-container mt-4">
    <div class="page-header">
        <div class="header-content">
            <div class="page-title fs-2">Reports</div>
            <div class="page-subtitle mb-3">Monitor and track every logged visitor</div>
        </div>
        <div class="top-button position-absolute top-50 end-0 translate-middle-y d-flex align-items-center justify-content-center
         text-white rounded-2 border-0 cursor-pointer px-3 py-2" data-bs-toggle="modal" data-bs-target="#filterModal">
            <i class="bi bi-funnel me-1"></i> Filter Report
        </div>
    </div>
    <!-- table.scss -->
    <div class="visitor-log-sheet-table table-responsive-sm table-responsive-md table-responsive-lg bg-white">
        <!-- search and filter -->
        <x-table-filter/>

        <!-- table -->
        <table class="table table-bordered align-middle" id="reportTable">
            <thead></thead>
        </table>

        <!-- Pagination -->
        <x-table-pagination/>
    </div>
</div>

@endsection

