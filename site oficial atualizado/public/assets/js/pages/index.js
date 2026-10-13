
document.addEventListener('DOMContentLoaded', () => {
    iniciarHeroSlider();
    initCoverFlow();
});

const HERO_DATA = [
    {
        img: './assets/img/pages/index/slides/slide_01.png',
        title: 'Deltarune',
        desc: 'Mergulhe em um novo capítulo do multiverso de Toby Fox. Uma narrativa emocionante e subversiva que redefine os limites do RPG indie com carisma e mistério.',
        status: 'Última Atualização',
        jogoId: 5,
        slug: 'deltarune',
    },
    {
        img: './assets/img/pages/index/slides/slide_02.png',
        title: 'Celeste',
        desc: 'Uma obra-prima de plataforma sobre autodescoberta. Supere montanhas físicas e metafóricas em uma jornada sensível, precisa e visualmente deslumbrante.',
        status: 'Aclamado pela Crítica',
        jogoId: 6,
        slug: 'celeste',
    },
    {
        img: './assets/img/pages/index/slides/slide_03.png',
        title: 'Hades II',
        desc: 'A batalha além do Submundo continua. Domine feitiçarias ancestrais e enfrente o Titã do Tempo nesta sequência eletrizante do roguelite mais premiado da Supergiant.',
        status: 'Early Access',
        jogoId: 4,
        slug: 'hades-ii',
    },
    {
        img: './assets/img/pages/index/slides/slide_04.png',
        title: 'Journey',
        desc: 'Uma odisseia visual e sonora pelas areias de um mundo ancestral. Descubra a beleza do silêncio e a conexão humana em uma das experiências mais artísticas dos games.',
        status: 'Early Access',
        jogoId: 7,
        slug: 'journey',
    },
    {
        img: './assets/img/pages/index/slides/slide_05.png',
        title: 'Stardew Valley',
        desc: 'Cultive muito mais que uma fazenda. Construa laços, explore segredos e encontre o seu refúgio ideal neste simulador de vida que transborda charme e profundidade.',
        status: 'Disponível',
        jogoId: 8,
        slug: 'stardew-valley',
    }
];

let slideAtual = 0;
let slideInterval;

function iniciarHeroSlider() {
    const sideItems = document.querySelectorAll('.side-item');
    const mainImg = document.querySelector('.hero-main-img');
    const heroContent = document.querySelector('.hero-overlay-content');
    const heroDots = document.querySelectorAll('.hero-dot');

    if (!mainImg || !heroContent || sideItems.length === 0) return;

    comecarAutoPlay(sideItems, mainImg, heroContent, heroDots);

    sideItems.forEach((item, index) => {
        item.addEventListener('click', () => {
            resetarAutoPlay();
            trocarSlide(index, sideItems, mainImg, heroContent, heroDots);
            comecarAutoPlay(sideItems, mainImg, heroContent, heroDots);
        });
    });

    iniciarWishlist();
    atualizarWishlist(HERO_DATA[slideAtual].jogoId);
    atualizarPlayBtn(HERO_DATA[slideAtual].slug);
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
