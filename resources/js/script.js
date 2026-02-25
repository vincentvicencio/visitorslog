$(document).ready(function () {
    // Sidebar toggle
    // const sidebarToggle = document.getElementById('sidebarToggle');
    // const sideMenu = document.getElementById('sideMenu');
    //     sidebarToggle.addEventListener('click', () => {
    //         sideMenu.classList.toggle('show');
    //         sidebarToggle.classList.toggle('show');
    //     });

        $('sidebarToggle').on('click', function () {
            $('#sideMenu').toggleClass('show');
            $('#sidebarToggle').toggleClass('show');
        });
});
