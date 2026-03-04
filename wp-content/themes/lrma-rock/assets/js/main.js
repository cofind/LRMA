(function () {
  'use strict';

  // ─── Header scroll ─────────────────────────────────────────────────────────
  const header = document.getElementById('site-header');
  if (header) {
    const onScroll = () => {
      header.classList.toggle('scrolled', window.scrollY > 60);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // ─── Mobile menu ───────────────────────────────────────────────────────────
  const toggle = document.getElementById('nav-toggle');
  const mobileMenu = document.getElementById('mobile-menu');
  if (toggle && mobileMenu) {
    toggle.addEventListener('click', () => {
      const open = mobileMenu.classList.toggle('open');
      toggle.classList.toggle('open', open);
      document.body.classList.toggle('menu-open', open);
    });
    // Close on link click
    mobileMenu.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', () => {
        mobileMenu.classList.remove('open');
        toggle.classList.remove('open');
        document.body.classList.remove('menu-open');
      });
    });
  }

  // ─── Radio player ──────────────────────────────────────────────────────────
  const streamUrl = (typeof lrmaData !== 'undefined') ? lrmaData.radioStream : '';
  let audio = null;
  let isPlaying = false;

  const PLAY_ICON  = '<path d="M8 5v14l11-7z"/>';
  const PAUSE_ICON = '<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>';

  function getPlayBtns() {
    return document.querySelectorAll('[data-radio-btn]');
  }
  function getEqualizers() {
    return document.querySelectorAll('[data-radio-eq]');
  }

  function updateUI(playing) {
    getPlayBtns().forEach(btn => {
      const svg = btn.querySelector('svg');
      if (svg) svg.innerHTML = playing ? PAUSE_ICON : PLAY_ICON;
      btn.classList.toggle('playing', playing);
    });
    getEqualizers().forEach(eq => eq.classList.toggle('playing', playing));
  }

  function initAudio() {
    if (audio) return;
    audio = new Audio(streamUrl);
    audio.preload = 'none';
    audio.addEventListener('error', () => {
      console.warn('LRMA Radio: stream error');
    });
  }

  function toggleRadio() {
    initAudio();
    if (isPlaying) {
      audio.pause();
      audio.src = ''; // Stop buffering
      audio.src = streamUrl;
      isPlaying = false;
      updateUI(false);
    } else {
      const p = audio.play();
      if (p !== undefined) {
        p.then(() => {
          isPlaying = true;
          updateUI(true);
        }).catch(() => {
          isPlaying = false;
          updateUI(false);
        });
      } else {
        isPlaying = true;
        updateUI(true);
      }
    }
  }

  document.addEventListener('click', e => {
    const btn = e.target.closest('[data-radio-btn]');
    if (btn) {
      e.preventDefault();
      toggleRadio();
    }
  });

  // ─── Volume slider ─────────────────────────────────────────────────────────
  const volumeSlider = document.getElementById('radio-volume');
  if (volumeSlider) {
    volumeSlider.addEventListener('input', () => {
      if (audio) audio.volume = volumeSlider.value;
    });
  }

  // ─── Ticker duplicate for seamless loop ─────────────────────────────────────
  const track = document.querySelector('.ticker-track');
  if (track) {
    const clone = track.cloneNode(true);
    track.parentNode.appendChild(clone);
  }

  // ─── Raksti tabs (client-side, no page reload) ───────────────────────────────
  const tabBtns   = document.querySelectorAll('.lrma-tab');
  const tabPanels = document.querySelectorAll('.lrma-tab-panel');
  if (tabBtns.length) {
    tabBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const target = btn.dataset.tab;
        tabBtns.forEach(t => {
          t.classList.toggle('is-active', t.dataset.tab === target);
          t.setAttribute('aria-selected', t.dataset.tab === target ? 'true' : 'false');
        });
        tabPanels.forEach(p => {
          p.classList.toggle('is-active', p.id === 'lrma-tab-' + target);
        });
      });
    });
  }

})();
