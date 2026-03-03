<?php
/**
 * Template Name: Radio Page
 * Template Post Type: page
 */
get_header();

$meta_url = esc_js( get_theme_mod( 'radio_meta_url', 'https://c22.radioboss.fm/w/nowplayinginfo?u=318' ) );
?>

<!-- ╔══════════════════════════════════════╗
     ║  CENTRAL PLAYER                     ║
     ╚══════════════════════════════════════╝ -->
<section class="rp-stage">
    <div class="rp-glow" aria-hidden="true"></div>
    <img class="rp-banner-img"
         src="http://207.154.226.128/wp-content/uploads/2026/03/radio_baneris2-removebg-preview-414x285x49x0x316x285x1707327822-1.png"
         alt="" aria-hidden="true">

    <!-- ── APP DOWNLOAD LINKS ── -->
    <div class="rp-app-links">
        <a href="https://ej.uz/LRMA_Rock_Radio_iOS" target="_blank" rel="noopener" class="rp-app-btn">
            <svg viewBox="0 0 24 24" fill="currentColor" width="15" height="15" aria-hidden="true">
                <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
            </svg>
            App Store
        </a>
        <a href="https://ej.uz/LRMA_Rock_Radio_Android" target="_blank" rel="noopener" class="rp-app-btn">
            <svg viewBox="0 0 24 24" fill="currentColor" width="15" height="15" aria-hidden="true">
                <path d="M3 20.5v-17c0-.91 1.06-1.37 1.74-.73L20 12 4.74 21.23C4.06 21.87 3 21.41 3 20.5zM16.81 15.12L6.05 21.34l8.49-8.49 2.27 2.27zM20.16 10.81c.34.27.59.69.59 1.19s-.22.9-.57 1.18l-2.29 1.32-2.5-2.5 2.5-2.5 2.27 1.31zM6.05 2.66l10.76 6.22-2.27 2.27-8.49-8.49z"/>
            </svg>
            Google Play
        </a>
    </div>

    <div class="rp-player">

        <!-- ── PREVIOUS TRACK ── -->
        <div class="rp-track rp-track--side rp-track--prev">
            <div class="rp-track-dir">← Iepriekšējā</div>
            <div class="rp-track-body">
                <span class="rp-track-artist" id="rpPrevArtist">—</span>
                <span class="rp-track-title"  id="rpPrevTitle"></span>
            </div>
        </div>

        <!-- ── CENTER: waveform + now playing + controls ── -->
        <div class="rp-center">
            <div class="rp-wave" id="rpWave">
                <?php
                $bars   = [22,48,35,62,28,75,42,58,30,68,45,52,36,78,24,56,40,65,32,50,44,72,26,60];
                foreach ( $bars as $i => $h ) :
                    $delay = round( $i * 0.055, 3 );
                ?>
                <div class="rp-wbar" style="height:<?php echo $h; ?>px;animation-delay:<?php echo $delay; ?>s"></div>
                <?php endforeach; ?>
            </div>

            <p class="rp-now-artist" id="rpArtist">LRMA Rock Radio</p>
            <p class="rp-now-song"   id="rpSong">Ielādē straumējumu&hellip;</p>

            <div class="rp-controls">
                <div class="rp-vol-row">
                    <span class="rp-vol-lbl">VOL</span>
                    <div class="rp-vol-track">
                        <input type="range" id="rpVol" class="rp-vol-range"
                               min="0" max="1" step="0.01" value="0.8"
                               aria-label="Skaļums">
                    </div>
                    <svg class="rp-vol-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
                        <path d="M15.54 8.46a5 5 0 0 1 0 7.07"/>
                        <path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>
                    </svg>
                </div>

                <div class="rp-listeners" id="rpListeners"></div>

                <button class="rp-btn" id="rpPlayBtn" aria-label="Atskaņot radio">
                    <svg viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </button>
            </div>
        </div><!-- .rp-center -->

        <!-- ── NEXT TRACK ── -->
        <div class="rp-track rp-track--side rp-track--next">
            <div class="rp-track-dir">Nākamā →</div>
            <div class="rp-track-body">
                <span class="rp-track-artist" id="rpNextArtist">—</span>
                <span class="rp-track-title"  id="rpNextTitle"></span>
            </div>
        </div>

    </div><!-- .rp-player -->
</section>

<!-- ╔══════════════════════════════════════╗
     ║  RADIO PAGE SCRIPT                  ║
     ╚══════════════════════════════════════╝ -->
<script>
(function () {
    var META_URL = '<?php echo $meta_url; ?>';
    var POLL_MS  = 10000;

    var audio   = document.getElementById('radioAudio');
    var playBtn = document.getElementById('rpPlayBtn');
    var wave    = document.getElementById('rpWave');
    var volRng  = document.getElementById('rpVol');
    var lstEl   = document.getElementById('rpListeners');

    var elArtist = document.getElementById('rpArtist');
    var elSong   = document.getElementById('rpSong');
    var elPrevA  = document.getElementById('rpPrevArtist');
    var elPrevT  = document.getElementById('rpPrevTitle');
    var elNextA  = document.getElementById('rpNextArtist');
    var elNextT  = document.getElementById('rpNextTitle');

    /* ── UI sync ─────────────────────────────────────────────── */
    function syncUI() {
        var on = audio && !audio.paused;
        if (playBtn) {
            playBtn.classList.toggle('active', on);
            var svg = playBtn.querySelector('svg');
            if (svg) svg.innerHTML = on
                ? '<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>'
                : '<path d="M8 5v14l11-7z"/>';
        }
        if (wave) wave.classList.toggle('playing', on);
    }

    if (audio) {
        audio.addEventListener('play',  syncUI);
        audio.addEventListener('pause', syncUI);
        audio.addEventListener('ended', syncUI);
        syncUI();
    }

    /* ── Play / pause ────────────────────────────────────────── */
    if (playBtn && audio) {
        playBtn.addEventListener('click', function () {
            if (audio.paused) { audio.play().catch(function () {}); }
            else              { audio.pause(); }
        });
    }

    /* ── Volume ──────────────────────────────────────────────── */
    if (volRng && audio) {
        audio.volume = parseFloat(volRng.value);
        volRng.addEventListener('input', function () {
            audio.volume = parseFloat(volRng.value);
        });
    }

    /* ── Track metadata ──────────────────────────────────────── */
    var lastNow  = '';
    var prevData = null;

    function parse(str) {
        if (!str) return { artist: '—', title: '' };
        var i = str.indexOf(' - ');
        if (i > -1) return { artist: str.slice(0, i).trim(), title: str.slice(i + 3).trim() };
        return { artist: str.trim(), title: '' };
    }

    function setText(el, v) { if (el) el.textContent = v || ''; }

    function fadeSwap(fn) {
        [elArtist, elSong].forEach(function (e) { if (e) e.classList.add('rp-fade'); });
        setTimeout(function () {
            fn();
            [elArtist, elSong].forEach(function (e) { if (e) e.classList.remove('rp-fade'); });
        }, 350);
    }

    function fetchMeta() {
        if (!META_URL) return;
        fetch(META_URL)
            .then(function (r) { return r.json(); })
            .then(function (d) {
                var now  = d.nowplaying   || d.currenttrack || d.autodj_title || d.track || d.title || '';
                var next = d.nextsong     || d.next_song    || d.next         || '';
                var cnt  = d.listeners    || d.listenercount || '';

                if (now && now !== lastNow) {
                    if (lastNow) prevData = parse(lastNow);
                    lastNow = now;
                    var cur = parse(now);
                    fadeSwap(function () {
                        setText(elArtist, cur.artist);
                        setText(elSong,   cur.title);
                        if (prevData) {
                            setText(elPrevA, prevData.artist);
                            setText(elPrevT, prevData.title);
                        }
                    });
                    /* Keep header/mobile track displays in sync */
                    var hTrack = document.getElementById('headerRadioTrack');
                    var mTrack = document.getElementById('mobileRadioTrack');
                    if (hTrack) hTrack.textContent = now;
                    if (mTrack) mTrack.textContent = now;
                } else if (!lastNow && now) {
                    lastNow = now;
                    var cur = parse(now);
                    setText(elArtist, cur.artist);
                    setText(elSong,   cur.title);
                }

                if (next) {
                    var nxt = parse(next);
                    setText(elNextA, nxt.artist);
                    setText(elNextT, nxt.title);
                }

                if (cnt && lstEl) lstEl.textContent = cnt + ' klausītāji';
            })
            .catch(function () {});
    }

    fetchMeta();
    setInterval(fetchMeta, POLL_MS);
})();
</script>

<?php get_footer(); ?>
