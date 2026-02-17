$(document).ready(function () {
    // Sidebar toggle (only runs if elements exist)
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sideMenu = document.getElementById('sideMenu');
    if (sidebarToggle && sideMenu) {
        sidebarToggle.addEventListener('click', () => {
            sideMenu.classList.toggle('show');
            sidebarToggle.classList.toggle('show');
        });
    }

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
    }
});
