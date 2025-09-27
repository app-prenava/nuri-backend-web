/*!
* Start Bootstrap - SB Admin v7.0.5 (https://startbootstrap.com/template/sb-admin)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
*/

// Initialize sidebar toggle
window.addEventListener('DOMContentLoaded', event => {
    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }

    // Apply active state to nav links based on URL pathname
    const activateNavItem = () => {
        const path = window.location.pathname;
        const navLinks = document.querySelectorAll('.sb-sidenav .nav-link');
        
        navLinks.forEach(link => {
            if (link.getAttribute('href') === path) {
                link.classList.add('active');
            }
        });
    };
    
    activateNavItem();
});