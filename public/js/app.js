// OsunBiz — Application JavaScript (Phase 3)
// Minimal interactivity: mobile menu toggle, form enhancements

(function () {
    'use strict';

    // Mobile hamburger menu
    var menuToggle = document.querySelector('.menu-toggle');
    var nav = document.querySelector('.nav');

    if (menuToggle && nav) {
        menuToggle.addEventListener('click', function () {
            var isOpen = nav.classList.toggle('open');
            menuToggle.setAttribute('aria-expanded', isOpen);
        });

        // Close menu when clicking a nav link
        var navLinks = nav.querySelectorAll('a');
        navLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                nav.classList.remove('open');
                menuToggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    // Add menu toggle button to header on mobile (JS-injected for progressive enhancement)
    if (!document.querySelector('.menu-toggle')) {
        var headerInner = document.querySelector('.header-inner');
        var navEl = document.querySelector('.nav');
        if (headerInner && navEl) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'menu-toggle';
            btn.setAttribute('aria-label', 'Toggle navigation');
            btn.setAttribute('aria-expanded', 'false');
            btn.innerHTML = '&#9776;';
            headerInner.appendChild(btn);
            menuToggle = btn;
            nav = navEl;
            menuToggle.addEventListener('click', function () {
                var isOpen = nav.classList.toggle('open');
                menuToggle.setAttribute('aria-expanded', isOpen);
            });
        }
    }

    // Simple form enhancement: submit button loading state
    var forms = document.querySelectorAll('form');
    forms.forEach(function (form) {
        form.addEventListener('submit', function () {
            var btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.originalText = btn.textContent;
                btn.textContent = 'Processing…';
            }
        });
    });
})();