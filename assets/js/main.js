// Mobile navigation toggle
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.querySelector('.nav-toggle');
    const navLinks = document.querySelector('.navbar ul');

    if (toggleBtn && navLinks) {
        toggleBtn.addEventListener('click', function () {
            navLinks.classList.toggle('nav-open');
        });
    }
});