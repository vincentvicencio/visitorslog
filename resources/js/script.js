$(document).ready(function () {
    // Sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sideMenu = document.getElementById('sideMenu');
<<<<<<< HEAD
 
    if (sidebarToggle && sideMenu) {
=======
>>>>>>> 75c44668e6d6ef9ac1cfee5591451d7eb505707e
        sidebarToggle.addEventListener('click', () => {
            sideMenu.classList.toggle('show');
            sidebarToggle.classList.toggle('show');
        });
<<<<<<< HEAD
=======

    // Password toggle (only runs if elements exist)
    const passwordInput  = document.getElementById('passwordInput');
    const togglePassword = document.getElementById('togglePassword');
    const eyeIcon        = document.getElementById('eyeIcon');
    if (passwordInput && togglePassword && eyeIcon) {
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.classList.toggle('bi-eye-fill');
            eyeIcon.classList.toggle('bi-eye-slash-fill');
        });
>>>>>>> 75c44668e6d6ef9ac1cfee5591451d7eb505707e
    }
});
