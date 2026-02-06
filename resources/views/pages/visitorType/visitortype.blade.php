@extends('layout')

@section('content')
<div class="user-types-container mt-4">
    <div class="page-header">
        <div class="header-content">
            <div class="page-title fs-2">Visitor Type</div>
            <div class="page-subtitle mb-3">Manage and organize different visitor categories</div>
        </div>
        <div class="top-button position-absolute top-50 end-0 translate-middle-y d-flex align-items-center justify-content-center
         text-white rounded-2 border-0 cursor-pointer px-3 py-2" id="addBtn">
            Add Visitor Type
        </div>
    </div>
    <!-- table.scss -->
    <div class="visitor-log-sheet-table table-responsive-sm table-responsive-md table-responsive-lg bg-white">
        <!-- search and filter -->
        <x-table-filter/>

        <!-- table -->
        <table class="table table-bordered align-middle" id="visitorsTable">
            <thead></thead>
        </table>

        <!-- Pagination -->
        <x-table-pagination/>
    </div>

    

    
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


@push('scripts')
    @vite(['resources/js/visitortypeClass.js', 'resources/js/visitortype.js'])
@endpush

@endsection

