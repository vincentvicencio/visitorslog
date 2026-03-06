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
    <div class="visitor-log-sheet-table table-responsive-sm table-responsive-md table-responsive-lg bg-white">
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
@include('components.triggers.users-userstype-toast')
@include('components.triggers.reportModals')

@endsection

@push('scripts')
@vite(['resources/js/report.js'])
@endpush