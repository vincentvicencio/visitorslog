$(document).ready(function () {

    const sidebarToggle = document.getElementById('sidebarToggle');
    const sideMenu = document.getElementById('sideMenu');

    sidebarToggle.addEventListener('click', function (e) {
        e.stopPropagation();
        sideMenu.classList.toggle('show');
        sidebarToggle.classList.toggle('show');
    });

    sideMenu.addEventListener('click', function (e) {
        e.stopPropagation();
    });

    document.addEventListener('click', function () {
        if (sideMenu.classList.contains('show')) {
            sideMenu.classList.remove('show');
            sidebarToggle.classList.remove('show');
        }
    });

});