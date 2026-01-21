@extends('layout')

@section('content')
<div class="user-types-container mt-4">
    <div class="page-header">
        <div class="header-content" style= "background-color:'red';">
            <h2 class="page-title">Visitor Log Sheets</h2>
            <p class="page-subtitle mb-3">Manage and track all visitor entries</p>
            
        </div>
    </div>

    <div class="table-card py-4">
        <div class="table-container table-responsive-sm table-responsive-md table-responsive-lg">
            <table class="table modern-table border border-dark-subtle" id="visitorsTable">
            </table>
        </div>
    </div>
</div>

@endsection
