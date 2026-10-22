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

  /* ---------- Lista de Desejos (salvar jogo) ---------- */
  const wishlistBtns = document.querySelectorAll('.btn-wishlist[data-jogo-id]');

  wishlistBtns.forEach(btn => {
    btn.addEventListener('click', async () => {
      const jogoId = btn.dataset.jogoId;
      if (!jogoId || jogoId === '0') return;

      btn.disabled = true;

      try {
        const resp = await fetch(window.FLOAT.apiUrl('user/salvar_jogo.php'), {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'jogo_id=' + encodeURIComponent(jogoId)
        });

        if (resp.status === 401) {
          window.location.href = window.FLOAT.url('pages/login.php');
          return;
        }

        const data = await resp.json();
        if (!data.ok) throw new Error(data.mensagem || 'Erro ao salvar.');

        const salvo = data.acao === 'salvo';

        document.querySelectorAll('.btn-wishlist[data-jogo-id="' + jogoId + '"]').forEach(b => {
          b.classList.toggle('active', salvo);

          const icon = b.querySelector('i');
          if (icon) {
            icon.classList.toggle('fa-solid', salvo);
            icon.classList.toggle('fa-regular', !salvo);
          }

          const label = b.querySelector('.btn-wishlist-label');
          if (label) {
            label.textContent = salvo ? 'Salvo na Lista de Desejos' : 'Adicionar à Lista de Desejos';
          }
        });
      } catch (err) {
        alert('Não foi possível atualizar a lista de desejos. Tente novamente.');
      } finally {
        btn.disabled = false;
      }
    });
  });

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
