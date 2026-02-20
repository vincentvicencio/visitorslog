@extends('layout')

@section('content')

<div class="user-types-container mt-4">
    {{-- header --}}
    <div class="page-header">
        <div class="header-content">
            <div class="page-title fs-2">Visitor Log Sheets</div>
            <div class="page-subtitle mb-3">Manage and track all visitor entries</div>
        </div>
        <a class="top-button position-absolute top-50 end-0 translate-middle-y d-flex align-items-center justify-content-center
            text-white rounded-2 border-0 cursor-pointer px-3 py-2 text-decoration-none" href="{{ route('visitorslog.form') }}" id="addBtn">
            Add Visitor
        </a>
    </div>
    <!-- table holder -->
    <div class="visitor-log-sheet-table table-responsive ">
        <!-- search and filter -->
        <x-table-filter/>

        <!-- table -->
        <table class="table table-bordered align-middle" id="visitorsLogTable"><thead></thead></table>
    </div>
</div>

@push('scripts')
@vite(['resources/js/visitors.js'])
@endpush
@endsection
