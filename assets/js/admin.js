document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.querySelector('.admin-nav-toggle');
    const nav = document.querySelector('.admin-nav');

    if (toggleBtn && nav) {
        toggleBtn.addEventListener('click', function () {
            nav.classList.toggle('nav-open');
        });
    }
});