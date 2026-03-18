import visitors from './visitors';
import employees from './employees';
import reports from './report';

$(document).ready(function () {

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
        container.classList.add("table-tab");
        Object.entries(tabs).forEach(([key, tab]) => {
            const sel = key === selected;
            tab.classList.toggle("selected", sel);
            tab.classList.toggle("notselected", !sel);
            tab.querySelectorAll(".curve").forEach(c => c.remove());
            if (!sel) return;
            if (key === "visitor"){ 
                tab.appendChild(createCurve("right")); 
            }
            if (key === "employee") { 
                tab.appendChild(createCurve("left")); 
                tab.appendChild(createCurve("right")); 
            }
        });
    };

    // updateView(tabs.employee.classList.contains("selected") ? "employee" : "visitor");
    const navLinks = document.querySelectorAll(".sidebar-menu-button");

    navLinks.forEach(link => {
        link.addEventListener("click", () => {
            const tab = link.getAttribute("data-tab");
            if(tab === "visitor") {
                sessionStorage.setItem("logtab", "visitor");
            }
            sessionStorage.setItem("logtab", "visitor");
        });
    });

    const logoutButton = document.getElementById('logout');
    logoutButton.addEventListener('click', () => {
        sessionStorage.setItem('logtab', 'visitor');
    });


    updateView(sessionStorage.getItem('logtab') || "visitor");

    if ($.fn.DataTable.isDataTable('#visitorsLogTable')) {
        $('#visitorsLogTable').DataTable().destroy();
        $('#visitorsLogTable').empty();
    }

    if ($.fn.DataTable.isDataTable('#reportTable')) {
        $('#reportTable').DataTable().destroy();
        $('#reportTable').empty();
    }

    if(sessionStorage.getItem('logtab') === 'employee') {
        employees.onLoadPage();
        reports.onLoadPage();
        $('#addBtn').addClass('d-none');
        $('#addBtnEmp').removeClass('d-none');
    }else{
        visitors.onLoadPage();
        reports.initializePage();
        $('#addBtn').removeClass('d-none');
        $('#addBtnEmp').addClass('d-none');
    }

    

    Object.keys(tabs).forEach(k => {
        tabs[k].addEventListener("click", () => {
            updateView(k);

            if ($.fn.DataTable.isDataTable('#visitorsLogTable')) {
                $('#visitorsLogTable').DataTable().destroy();
                $('#visitorsLogTable').empty();
            }

            if ($.fn.DataTable.isDataTable('#reportTable')) {
                $('#reportTable').DataTable().destroy();
                $('#reportTable').empty();
            }

            if (k === "visitor") {
                visitors.onLoadPage();
                reports.initializePage();
                $('#addBtn').removeClass('d-none');
                $('#addBtnEmp').addClass('d-none');
                sessionStorage.setItem('logtab', 'visitor');
            }

            if (k === "employee") {
                employees.onLoadPage();
                reports.onLoadPage();
                $('#addBtn').addClass('d-none');
                $('#addBtnEmp').removeClass('d-none');
                sessionStorage.setItem('logtab', 'employee');
            }
        });
    });
});