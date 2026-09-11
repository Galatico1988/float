
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
    },
    {
        img: './assets/img/pages/index/slides/slide_02.png',
        title: 'Celeste',
        desc: 'Uma obra-prima de plataforma sobre autodescoberta. Supere montanhas físicas e metafóricas em uma jornada sensível, precisa e visualmente deslumbrante.',
        status: 'Aclamado pela Crítica',
    },
    {
        img: './assets/img/pages/index/slides/slide_03.png',
        title: 'Hades II',
        desc: 'A batalha além do Submundo continua. Domine feitiçarias ancestrais e enfrente o Titã do Tempo nesta sequência eletrizante do roguelite mais premiado da Supergiant.',
        status: 'Early Access',
    },
    {
        img: './assets/img/pages/index/slides/slide_04.png',
        title: 'Journey',
        desc: 'Uma odisseia visual e sonora pelas areias de um mundo ancestral. Descubra a beleza do silêncio e a conexão humana em uma das experiências mais artísticas dos games.',
        status: 'Early Access',
    },
    {
        img: './assets/img/pages/index/slides/slide_05.png',
        title: 'Stardew Valley',
        desc: 'Cultive muito mais que uma fazenda. Construa laços, explore segredos e encontre o seu refúgio ideal neste simulador de vida que transborda charme e profundidade.',
        status: 'Disponível',
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
