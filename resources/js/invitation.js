const envelope = document.querySelector('#envelope');
const paper = document.querySelector('#invitation');
const openButton = document.querySelector('#open-invitation');
const toggle = document.querySelector('#scroll-toggle');
const navigation = document.querySelector('#invitation-nav');
const menuToggle = document.querySelector('#nav-menu-toggle');
const music = document.querySelector('#invitation-music');
const musicToggle = document.querySelector('#music-toggle');
const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
let frame = null;

function syncMusicControl() {
    if (!music || !musicToggle) return;
    const playing = !music.paused && !music.ended && !music.error;
    const label = playing ? 'Pause music' : 'Play music';
    musicToggle.querySelector('[data-music-label]').textContent = label;
    musicToggle.setAttribute('aria-label', label);
    musicToggle.setAttribute('aria-pressed', String(playing));
    musicToggle.classList.toggle('is-playing', playing);
}

function playMusic() {
    if (!music) return;
    if (music.error) music.load();
    music.play().catch(() => {
        // Browsers can require another tap before allowing audio playback.
        syncMusicControl();
    });
}

if (music && musicToggle) {
    music.volume = 0.35;
    ['play', 'pause', 'ended', 'error'].forEach(event => music.addEventListener(event, syncMusicControl));
    musicToggle.addEventListener('click', () => {
        if (music.paused || music.error) playMusic();
        else music.pause();
    });
}

function pauseScroll() {
    if (frame !== null) cancelAnimationFrame(frame);
    frame = null;
    if (toggle) {
        toggle.setAttribute('aria-label', 'Start automatic scrolling');
        toggle.setAttribute('aria-pressed', 'false');
        toggle.textContent = '▷';
    }
}

function updateNavHeight() {
    if (navigation && !navigation.hidden) {
        document.documentElement.style.setProperty('--nav-height', `${navigation.getBoundingClientRect().height}px`);
    }
}

if (navigation && menuToggle) {
    const links = [...navigation.querySelectorAll('.invitation-links a')];
    const sections = links.map(link => document.getElementById(link.hash.slice(1)));
    const mobileMenu = window.matchMedia('(max-width: 899px)');
    const closeMenu = () => {
        navigation.classList.remove('is-open');
        menuToggle.setAttribute('aria-expanded', 'false');
    };
    navigation.classList.add('nav-ready');
    menuToggle.hidden = false;
    menuToggle.addEventListener('click', () => {
        const open = navigation.classList.toggle('is-open');
        menuToggle.setAttribute('aria-expanded', String(open));
    });
    navigation.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
        pauseScroll();
        closeMenu();
    }));
    navigation.addEventListener('keydown', event => {
        if (event.key === 'Escape' && navigation.classList.contains('is-open')) {
            closeMenu();
            menuToggle.focus();
        }
    });
    document.addEventListener('click', event => { if (!navigation.contains(event.target)) closeMenu(); });
    mobileMenu.addEventListener('change', closeMenu);
    new ResizeObserver(() => {
        updateNavHeight();
        requestAnimationFrame(updateCurrentSection);
    }).observe(navigation);
    updateNavHeight();

    let scheduled = false;
    const updateCurrentSection = () => {
        scheduled = false;
        if (navigation.hidden) return;
        const offset = navigation.getBoundingClientRect().height + 32;
        let current = 0;
        sections.forEach((section, index) => {
            if (section && section.getBoundingClientRect().top <= offset) current = index;
        });
        if (window.scrollY + window.innerHeight >= document.documentElement.scrollHeight - 2) current = links.length - 1;
        links.forEach((link, index) => {
            if (index === current) link.setAttribute('aria-current', 'location');
            else link.removeAttribute('aria-current');
        });
    };
    window.addEventListener('scroll', () => {
        if (!scheduled) { scheduled = true; requestAnimationFrame(updateCurrentSection); }
    }, {passive:true});
    window.addEventListener('load', updateCurrentSection);
    window.addEventListener('resize', updateCurrentSection);
}

if (paper && envelope) {
    let coverOpened = false;
    function openCover() {
        if (coverOpened) return;
        coverOpened = true;
        openButton.setAttribute('aria-disabled', 'true');
        openButton.textContent = 'Opening…';
        paper.hidden = false;
        paper.inert = true;
        if (navigation) navigation.hidden = false;
        if (navigation) navigation.inert = true;
        updateNavHeight();
        // Keep playback inside the opening click so mobile browsers allow music.
        playMusic();
        if (musicToggle) musicToggle.hidden = false;

        let finished = false;
        let fallback;
        let stageIndex = -1;
        const stages = [
            {name:'seal', duration:2000, animation:'wax-seal-turn', element:envelope.querySelector('.cover-seal')},
            {name:'names', duration:2000, animation:'cover-names-away', element:envelope.querySelector('.cover-names')},
            {name:'envelope', duration:3000, animation:'envelope-open', element:envelope},
        ];
        const finishOpening = () => {
            if (finished) return;
            finished = true;
            clearTimeout(fallback);
            envelope.removeEventListener('animationend', onOpeningEnd);
            envelope.removeEventListener('animationcancel', onOpeningCancel);
            envelope.hidden = true;
            envelope.classList.remove('is-opening');
            delete envelope.dataset.openingStage;
            paper.classList.remove('is-revealing');
            navigation?.classList.remove('is-revealing');
            document.documentElement.classList.remove('invitation-opening');
            paper.inert = false;
            if (navigation) navigation.inert = false;
            if (toggle) toggle.hidden = false;
            if (musicToggle) musicToggle.hidden = false;
            if (document.activeElement !== musicToggle) paper.focus({preventScroll:true});
        };
        const nextStage = () => {
            if (finished) return;
            clearTimeout(fallback);
            const stage = stages[++stageIndex];
            if (!stage) { finishOpening(); return; }
            envelope.dataset.openingStage = stage.name;
            if (stage.name === 'envelope') {
                paper.classList.add('is-revealing');
                navigation?.classList.add('is-revealing');
            }
            fallback = setTimeout(nextStage, stage.duration + 150);
        };
        const onOpeningEnd = event => {
            const stage = stages[stageIndex];
            if (event.target === stage?.element && event.animationName === stage.animation) nextStage();
        };
        const onOpeningCancel = event => {
            const stage = stages[stageIndex];
            if (event.target === stage?.element && event.animationName === stage.animation) finishOpening();
        };
        if (reduced.matches) {
            finishOpening();
        } else {
            stages.forEach(stage => envelope.style.setProperty(`--${stage.name}-duration`, `${stage.duration}ms`));
            document.documentElement.classList.add('invitation-opening');
            envelope.addEventListener('animationend', onOpeningEnd);
            envelope.addEventListener('animationcancel', onOpeningCancel);
            envelope.classList.add('is-opening');
            nextStage();
        }
    }
    if (window.location.hash) {
        coverOpened = true;
        envelope.hidden = true;
        if (toggle) toggle.hidden = false;
        if (musicToggle) musicToggle.hidden = false;
        document.getElementById(window.location.hash.slice(1))?.scrollIntoView({behavior:'instant'});
    } else {
        envelope.classList.add('ready');
        paper.hidden = true;
        if (navigation) navigation.hidden = true;
        openButton.focus({preventScroll:true});
    }
    openButton.addEventListener('click', event => { event.preventDefault(); openCover(); });
    envelope.addEventListener('keydown', event => { if (event.key === 'Escape') openCover(); });

    window.addEventListener('wheel', pauseScroll, {passive:true});
    ['pointerdown','touchstart','keydown','focusin'].forEach(type => document.addEventListener(type, event => {
        if (!toggle?.contains(event.target)) pauseScroll();
    }, {passive:true}));
    toggle?.addEventListener('click', () => {
        if (frame !== null) { pauseScroll(); return; }
        let position = window.scrollY;
        let previous = performance.now();
        toggle.setAttribute('aria-label','Pause automatic scrolling');
        toggle.setAttribute('aria-pressed','true');
        toggle.textContent = 'Ⅱ';
        const tick = now => {
            position += Math.min(80,now-previous)*.027;
            previous = now;
            window.scrollTo({top:position,behavior:'instant'});
            if (position >= document.documentElement.scrollHeight - document.documentElement.clientHeight) { pauseScroll(); return; }
            frame = requestAnimationFrame(tick);
        };
        frame = requestAnimationFrame(tick);
    });
    document.addEventListener('visibilitychange', () => { if (document.hidden) pauseScroll(); });
}

const countdown = document.querySelector('[data-countdown]');
if (countdown) {
    const target = Date.parse(countdown.dataset.countdown);
    const update = () => {
        const seconds = Math.max(0,Math.floor((target-Date.now())/1000));
        const units = {days:Math.floor(seconds/86400),hours:Math.floor(seconds/3600)%24,minutes:Math.floor(seconds/60)%60,seconds:seconds%60};
        Object.entries(units).forEach(([key,value]) => {countdown.querySelector(`[data-unit="${key}"]`).textContent=String(value).padStart(2,'0');});
        if (seconds) setTimeout(update,1000);
    };
    if (Number.isFinite(target)) update();
}

const gallery = [...document.querySelectorAll('[data-gallery]')];
const dialog = document.querySelector('#photo-dialog');
if (dialog && gallery.length) {
    let index=0;
    const show=indexToShow => {
        index=(indexToShow+gallery.length)%gallery.length;
        const photo=dialog.querySelector('img'); photo.src=gallery[index].href; photo.alt=gallery[index].querySelector('img').alt;
        dialog.querySelector('.photo-counter').textContent=`${index+1} / ${gallery.length}`;
    };
    gallery.forEach((link,i)=>link.addEventListener('click',event=>{event.preventDefault();pauseScroll();show(i);dialog.showModal();}));
    dialog.querySelector('.dialog-close').addEventListener('click',()=>dialog.close());
    dialog.querySelector('.dialog-previous').addEventListener('click',()=>show(index-1));
    dialog.querySelector('.dialog-next').addEventListener('click',()=>show(index+1));
    dialog.addEventListener('keydown',event=>{if(event.key==='ArrowLeft')show(index-1);if(event.key==='ArrowRight')show(index+1);});
    dialog.addEventListener('click',event=>{if(event.target===dialog)dialog.close();});
    dialog.addEventListener('close',()=>gallery[index].focus({preventScroll:true}));
}
