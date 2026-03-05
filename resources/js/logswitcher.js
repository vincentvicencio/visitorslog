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

});