import visitors from './visitors';
import employees from './employees';

$(document).ready(function () {

    $('#visitor').on('click', function () {

        console.log("Visitor clicked");

        if ($.fn.DataTable.isDataTable('#visitorsLogTable')) {
            $('#visitorsLogTable').DataTable().destroy();
            $('#visitorsLogTable').empty();
        }

        visitors.onLoadPage();

        $('#addBtn').removeClass('d-none');
        $('#addBtnEmp').addClass('d-none');

    });

    $('#employee').on('click', function () {

        console.log("Employee clicked");

        if ($.fn.DataTable.isDataTable('#visitorsLogTable')) {
            $('#visitorsLogTable').DataTable().destroy();
            $('#visitorsLogTable').empty();
        }

        employees.initializePage();

        $('#addBtn').addClass('d-none');
        $('#addBtnEmp').removeClass('d-none');

    });
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
            }
            if (key === "employee") { 
                tab.appendChild(createCurve("left")); 
                tab.appendChild(createCurve("right")); 
            }
        });
    };

    Object.keys(tabs).forEach(k => tabs[k].addEventListener("click", () => updateView(k)));
    updateView(tabs.employee.classList.contains("selected") ? "employee" : "visitor");

});