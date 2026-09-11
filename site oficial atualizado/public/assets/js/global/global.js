document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initMegaDropdowns();
    initDynamicNavbar();
    initScrollReveal();
    initSmoothScroll();
    initScrollToTopButton();
});

function initMobileMenu() {
    const menuToggle = document.getElementById('menu-toggle');
    const navMenu = document.getElementById('nav-menu');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            navMenu.classList.toggle('active');
            menuToggle.classList.toggle('open');
            document.body.classList.toggle('menu-open');

            if (navigator.vibrate) {
                navigator.vibrate(10);
            }
        });

        navMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                menuToggle.classList.remove('open');
                document.body.classList.remove('menu-open');
            });
        });

        document.addEventListener('click', (e) => {
            if (!navMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                navMenu.classList.remove('active');
                menuToggle.classList.remove('open');
                document.body.classList.remove('menu-open');
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
                menuToggle.classList.remove('open');
                document.body.classList.remove('menu-open');
            }
        });
    }
}

function initMegaDropdowns() {
    const navItems = document.querySelectorAll('.nav-item.has-dropdown');

    navItems.forEach(item => {
        const dropdown = item.querySelector('.mega-menu-wrapper');
        let hideTimeout;

        if (!dropdown) return;

        dropdown.style.display = '';
        dropdown.style.opacity = '';
        dropdown.style.pointerEvents = '';
        dropdown.style.transform = '';

        item.addEventListener('mouseenter', () => {
            clearTimeout(hideTimeout);

            if (window.innerWidth > 768) {
                dropdown.classList.add('is-active');

                requestAnimationFrame(() => {
                    dropdown.style.visibility = 'visible';
                    dropdown.style.pointerEvents = 'all';
                    dropdown.style.opacity = '1';
                    dropdown.style.transform = 'translateX(-50%) translateY(0) scale(1)';
                    dropdown.style.filter = 'blur(0px)';
                    dropdown.style.transition = 'all 0.5s cubic-bezier(0.16, 1, 0.3, 1)';
                });
            }
        });

        item.addEventListener('mouseleave', () => {
            if (window.innerWidth > 768) {
                hideTimeout = setTimeout(() => {
                    dropdown.style.opacity = '0';
                    dropdown.style.transform = 'translateX(-50%) translateY(15px) scale(0.98)';
                    dropdown.style.filter = 'blur(10px)';
                    dropdown.style.pointerEvents = 'none';

                    setTimeout(() => {
                        if (!dropdown.classList.contains('is-active')) {
                            dropdown.style.visibility = 'hidden';
                        }
                    }, 500);

                    dropdown.classList.remove('is-active');
                }, 150);
            }
        });

        const link = item.querySelector('.nav-link');
        link.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const isOpening = !dropdown.classList.contains('mobile-open');

                document.querySelectorAll('.mega-menu-wrapper').forEach(m => m.classList.remove('mobile-open'));

                if (isOpening) {
                    dropdown.classList.add('mobile-open');
                    dropdown.style.display = 'block';
                } else {
                    dropdown.classList.remove('mobile-open');
                    dropdown.style.display = 'none';
                }
            }
        });
    });
}

function initScrollReveal() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const revealOnScroll = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal-visible');
            }
        });
    }, observerOptions);

    const elementsToAnimate = document.querySelectorAll('.epic-card, .store-section, .footer-modern');
    elementsToAnimate.forEach(el => {
        el.classList.add('reveal-hidden');
        revealOnScroll.observe(el);
    });
}

function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
}

function initScrollToTopButton() {
    const btn = document.createElement('button');
    btn.className = 'btn-scroll-top';
    btn.innerHTML = '<i class="fas fa-arrow-up"></i>';


    document.body.appendChild(btn);

    window.addEventListener('scroll', () => {
        btn.style.display = window.pageYOffset > 300 ? 'flex' : 'none';
    });

    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    btn.addEventListener('mouseenter', () => {
        btn.style.transform = 'scale(1.15) translateY(-5px)';
    });

    btn.addEventListener('mouseleave', () => {
        btn.style.transform = 'scale(1)';
    });
}

function initDynamicNavbar() {
    const navbar = document.querySelector('.nav-wrapper');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            navbar.style.boxShadow = '0 20px 60px rgba(0, 0, 0, 0.9), 0 0 40px rgba(217, 244, 56, 0.2)';
            navbar.style.backgroundColor = 'rgba(10, 10, 15, 0.97)';
        } else {
            navbar.style.boxShadow = '0 10px 40px rgba(0, 0, 0, 0.8), inset 0 1px 20px rgba(217, 244, 56, 0.1)';
            navbar.style.backgroundColor = 'var(--nav-bg)';
        }
    });

    const observerOptions = {
        threshold: 0.1
    };

    const revealOnScroll = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal-visible');
            }
        });
    }, observerOptions);

    document.addEventListener('DOMContentLoaded', () => {
        const elementsToAnimate = document.querySelectorAll('.epic-card, .store-section, .footer-modern');

        elementsToAnimate.forEach(el => {
            el.classList.add('reveal-hidden');
            revealOnScroll.observe(el);
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    });


}

document.addEventListener('DOMContentLoaded', () => {
    const trigger = document.getElementById('profileTrigger');
    const dropdown = document.getElementById('profileDropdown');
    const btnScroll = document.querySelector('.btn-scroll-top');

    if (trigger && dropdown) {
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target) && e.target !== trigger) {
                dropdown.classList.remove('active');
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') dropdown.classList.remove('active');
        });
    }

    if (btnScroll) {
        window.addEventListener('scroll', () => {
            const btnScroll = document.querySelector('.btn-scroll-top');
            if (btnScroll) {
                if (window.scrollY > 400) {
                    btnScroll.classList.add('show');
                } else {
                    btnScroll.classList.remove('show');
                }
            }
        });

        btnScroll.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});