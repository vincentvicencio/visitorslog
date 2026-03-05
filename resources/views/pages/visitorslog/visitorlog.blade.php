@extends('layout')

@section('content')

<div class="user-types-container mt-4">
    {{-- header --}}
    <div class="page-header visitorlog">
        <div class="header-content">
            <div class="page-title fs-2">Log Sheets</div>
            <div class="page-subtitle mb-3">Manage and track all entries</div>
        </div>
        <a class="top-button position-absolute top-50 end-0 translate-middle-y d-flex align-items-center justify-content-center
            text-white rounded-2 border-0 cursor-pointer px-3 py-2 text-decoration-none" href="{{ route('visitorslog.form') }}" id="addBtn"  target="_blank"> 
            Add Visitor
        </a>
        <a class="top-button position-absolute top-50 end-0 translate-middle-y d-flex align-items-center justify-content-center
            text-white rounded-2 border-0 cursor-pointer px-3 py-2 text-decoration-none d-none" href="{{ route('visitorslog.form') }}" id="addBtnEmp" target="_blank"> 
            Log Employee
        </a>
    </div>
    <!-- table holder -->
    <div class="visitor-log-sheet-table table-tab">

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
        <table class="table table-bordered align-middle w-100" id="visitorsLogTable"><thead></thead></table>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const container = document.querySelector(".visitor-log-sheet-table");
        const tabs = {
            visitor: document.getElementById("visitor"),
            employee: document.getElementById("employee")
        };

        const createCurve = pos => Object.assign(document.createElement("div"), {
            className: `curve ${pos}`,
            innerHTML: '<div class="circle"></div>'
        });

        const updateView = selected => {
            container.classList.toggle("table-tab", selected === "visitor");
            Object.entries(tabs).forEach(([key, tab]) => {
                const sel = key === selected;
                tab.classList.toggle("selected", sel);
                tab.classList.toggle("notselected", !sel);
                tab.querySelectorAll(".curve").forEach(c => c.remove());
                if (!sel) return;
                if (key === "visitor"){ 
                    tab.appendChild(createCurve("right")); 
                    // $('addBtn').removeClass('d-none');
                    // $('addBtnEmp').addClass('d-none');
                }
                if (key === "employee") { 
                    tab.appendChild(createCurve("left")); 
                    tab.appendChild(createCurve("right")); 
                    // $('addBtn').addClass('d-none');
                    // $('addBtnEmp').removeClass('d-none');
                }
            });
        };

        Object.keys(tabs).forEach(k => tabs[k].addEventListener("click", () => updateView(k)));
        updateView(tabs.employee.classList.contains("selected") ? "employee" : "visitor");
    });
</script>
@push('scripts')
@vite(['resources/js/visitors.js'])
@endpush
@endsection
