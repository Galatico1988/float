
document.addEventListener('DOMContentLoaded', () => {
    iniciarHeroSlider();
    initCoverFlow();
    iniciarWishlist();
});

let HERO_DATA = [];

let slideAtual = 0;
let slideInterval;

function iniciarHeroSlider() {
    const sideItems = document.querySelectorAll('.side-item');
    const mainImg = document.querySelector('.hero-main-img');
    const heroContent = document.querySelector('.hero-overlay-content');
    const heroDots = document.querySelectorAll('.hero-dot');

    if (!mainImg || !heroContent || sideItems.length === 0) return;

    HERO_DATA = Array.from(sideItems, item => ({
        img: item.dataset.image,
        title: item.dataset.title,
        desc: item.dataset.description,
        status: item.dataset.status,
        jogoId: item.dataset.jogoId,
        slug: item.dataset.slug
    }));
    slideAtual = Math.max(0, Array.from(sideItems).findIndex(item => item.classList.contains('active')));
    atualizarWishlist(HERO_DATA[slideAtual].jogoId);
    atualizarPlayBtn(HERO_DATA[slideAtual].slug);

    if (HERO_DATA.length > 1) {
        comecarAutoPlay(sideItems, mainImg, heroContent, heroDots);
    }

    sideItems.forEach((item, index) => {
        item.addEventListener('click', () => {
            resetarAutoPlay();
            trocarSlide(index, sideItems, mainImg, heroContent, heroDots);
            if (HERO_DATA.length > 1) {
                comecarAutoPlay(sideItems, mainImg, heroContent, heroDots);
            }
        });
    });
}

function atualizarPlayBtn(slug) {
    const btn = document.getElementById('heroPlayBtn');
    if (!btn || !slug) return;
    btn.href = '/float/public/jogos/' + slug + '/';
}

function comecarAutoPlay(sideItems, mainImg, heroContent, heroDots) {
    slideInterval = setInterval(() => {
        slideAtual = (slideAtual + 1) % HERO_DATA.length;
        trocarSlide(slideAtual, sideItems, mainImg, heroContent, heroDots);
    }, 7000);
}

function resetarAutoPlay() {
    clearInterval(slideInterval);
}

function trocarSlide(index, sideItems, mainImg, heroContent, heroDots) {
    slideAtual = index;
    const dado = HERO_DATA[index];

    atualizarWishlist(dado.jogoId);
    atualizarPlayBtn(dado.slug);

    sideItems.forEach(i => i.classList.remove('active'));
    if (sideItems[index]) sideItems[index].classList.add('active');

    if (heroDots) {
        heroDots.forEach(d => d.classList.remove('active'));
        if (heroDots[index]) heroDots[index].classList.add('active');
    }

    mainImg.style.opacity = '0';
    heroContent.style.transform = 'translateX(-20px)';
    heroContent.style.opacity = '0';

    setTimeout(() => {
        mainImg.src = dado.img;
        mainImg.alt = dado.title;
        heroContent.querySelector('.hero-status').textContent = dado.status;
        heroContent.querySelector('.hero-game-name').textContent = dado.title;
        heroContent.querySelector('.hero-description').textContent = dado.desc;

        mainImg.style.opacity = '1';
        heroContent.style.transform = 'translateX(0)';
        heroContent.style.opacity = '1';
    }, 300);
}


function initCoverFlow() {
    const track = document.getElementById('coverflowTrack');
    const btnPrev = document.getElementById('coverflow-prev');
    const btnNext = document.getElementById('coverflow-next');

    if (!track || !btnPrev || !btnNext) return;

    const cards = Array.from(track.querySelectorAll('.cf-card'));
    let current = Math.floor(cards.length / 2);

    function applyStates() {
        cards.forEach((card, i) => {
            card.classList.remove('cf-active', 'cf-near', 'cf-near-left', 'cf-far-left');

            const diff = i - current;

            if (diff === 0) card.classList.add('cf-active');
            else if (diff === 1) card.classList.add('cf-near');
            else if (diff === -1) card.classList.add('cf-near-left');
            else if (diff < -1) card.classList.add('cf-far-left');
        });

        centerActiveCard();
    }

    function centerActiveCard() {
        const activeCard = cards[current];
        if (!activeCard) return;

        const wrapperWidth = track.parentElement.offsetWidth;
        const cardWidth = activeCard.offsetWidth;
        const offset = activeCard.offsetLeft - (wrapperWidth / 2) + (cardWidth / 2);

        track.style.transform = `translateX(-${offset}px)`;
    }

    function goTo(index) {
        current = Math.max(0, Math.min(index, cards.length - 1));
        applyStates();
    }

    cards.forEach((card, i) => {
        card.addEventListener('click', () => goTo(i));
    });

    btnPrev.addEventListener('click', () => goTo(current - 1));
    btnNext.addEventListener('click', () => goTo(current + 1));

    let touchStartX = 0;
    track.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', e => {
        const delta = touchStartX - e.changedTouches[0].clientX;
        if (Math.abs(delta) > 40) goTo(current + (delta > 0 ? 1 : -1));
    }, { passive: true });

    window.addEventListener('resize', () => centerActiveCard(), { passive: true });

    applyStates();
}

/* ---------- Lista de Desejos (hero) ---------- */
function atualizarWishlist(jogoId) {
    const btn = document.getElementById('heroWishlistBtn');
    if (!btn) return;

    btn.dataset.jogoId = jogoId || '';
    btn.classList.remove('active');

    const icon = btn.querySelector('i');
    if (icon) {
        icon.classList.remove('fa-solid');
        icon.classList.add('fa-regular');
    }
}

function iniciarWishlist() {
    const heroBtn = document.getElementById('heroWishlistBtn');
    const promoBtn = document.getElementById('promoWishlistBtn');

    [heroBtn, promoBtn].forEach(btn => {
        if (btn) btn.addEventListener('click', () => tratarWishlist(btn));
    });
}

async function tratarWishlist(btn) {
    const jogoId = btn.dataset.jogoId;
    if (!jogoId || jogoId === '0') return;

    btn.disabled = true;

    try {
        const resp = await fetch('/float/api/user/salvar_jogo.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'jogo_id=' + encodeURIComponent(jogoId)
        });

        if (resp.status === 401) {
            window.location.href = '/float/public/pages/login.php';
            return;
        }

        const data = await resp.json();
        if (!data.ok) throw new Error(data.mensagem || 'Erro ao salvar.');

        const salvo = data.acao === 'salvo';

        btn.classList.toggle('active', salvo);

        const icon = btn.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-solid', salvo);
            icon.classList.toggle('fa-regular', !salvo);
        }

        const label = btn.querySelector('.btn-wishlist-label');
        if (label) label.textContent = salvo ? 'Salvo na Lista de Desejos' : 'Lista de Desejos';
    } catch (err) {
        alert('Não foi possível atualizar a lista de desejos. Tente novamente.');
    } finally {
        btn.disabled = false;
    }
}
