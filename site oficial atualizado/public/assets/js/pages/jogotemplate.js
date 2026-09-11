document.addEventListener('DOMContentLoaded', () => {

  /* ---------- Galeria: troca a imagem principal ---------- */
  const galleryMain = document.getElementById('galleryMain');
  const thumbs = document.querySelectorAll('.gallery-thumb');

  thumbs.forEach(thumb => {
    thumb.addEventListener('click', () => {
      const src = thumb.getAttribute('data-src');
      if (!galleryMain || !src) return;

      galleryMain.style.opacity = '0';
      setTimeout(() => {
        galleryMain.src = src;
        galleryMain.style.opacity = '1';
      }, 150);

      thumbs.forEach(t => t.classList.remove('active'));
      thumb.classList.add('active');
    });
  });

  /* ---------- Barra fixa de download ao rolar ---------- */
  const stickyBar = document.getElementById('stickyDownloadBar');
  const heroSection = document.querySelector('.game-hero-section');

  if (stickyBar && heroSection) {
    const triggerPoint = () => heroSection.offsetTop + heroSection.offsetHeight;

    window.addEventListener('scroll', () => {
      if (window.scrollY > triggerPoint()) {
        stickyBar.classList.add('show');
      } else {
        stickyBar.classList.remove('show');
      }
    }, { passive: true });
  }

  /* ---------- Animação dos medidores de requisitos ---------- */
  const meterFills = document.querySelectorAll('.req-meter-fill');

  const meterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const value = el.getAttribute('data-value') || 0;
        el.style.width = value + '%';
        meterObserver.unobserve(el);
      }
    });
  }, { threshold: 0.4 });

  meterFills.forEach(fill => meterObserver.observe(fill));

  /* ---------- Reveal genérico (caso global.js não cubra esta página) ---------- */
  const revealEls = document.querySelectorAll('.reveal-hidden');
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('reveal-visible');
        revealObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  revealEls.forEach(el => revealObserver.observe(el));

});
