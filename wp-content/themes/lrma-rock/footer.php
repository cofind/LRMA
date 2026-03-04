</main>

<footer class="site-footer">

    <div class="lrma-newsletter">
        <div class="lrma-newsletter-text">
            <div class="lrma-newsletter-kicker">Jaunumi uz e-pastu</div>
            <div class="lrma-newsletter-title">Pieteikties</div>
            <p>Saņem jaunākās LRMA ziņas tieši savā e-pastā.</p>
        </div>
        <form class="lrma-newsletter-form">
            <input type="email" name="email" placeholder="jūsu@epasts.lv" required>
            <label class="lrma-newsletter-gdpr">
                <input type="checkbox" name="gdpr" required>
                Piekrītu datu apstrādei saskaņā ar <a href="<?php echo esc_url( home_url( '/privatuma-politika/' ) ); ?>">privātuma politiku</a>
            </label>
            <button type="submit">Pieteikties →</button>
            <div class="lrma-newsletter-error" id="lrma-newsletter-error" aria-live="polite"></div>
        </form>
    </div>

    <div class="footer-top">

        <div class="footer-brand">
            <?php
            $fl_id  = get_theme_mod( 'custom_logo' );
            $fl_url = $fl_id ? add_query_arg( 'v', $fl_id, wp_get_attachment_image_url( $fl_id, 'full' ) ) : '';
            ?>
            <div class="footer-logo">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php bloginfo( 'name' ); ?>">
                    <?php if ( $fl_url ) : ?>
                    <img src="<?php echo esc_url( $fl_url ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="footer-logo-img" loading="lazy">
                    <?php else : ?>
                    <span class="footer-logo-text">LR<em>M</em>A</span>
                    <?php endif; ?>
                </a>
            </div>
            <p class="footer-desc">Latvijas Rokmūzikas Asociācija — Baltijas lielākais roka mūzikas medijs un mākslinieku atbalsta organizācija kopš 2016. gada.</p>
            <a href="mailto:<?php echo esc_attr( get_theme_mod( 'site_email', 'info@lrma.lv' ) ); ?>" class="footer-email">
                <?php echo esc_html( get_theme_mod( 'site_email', 'info@lrma.lv' ) ); ?>
            </a>
            <div class="social-links">
                <?php
                $fb = get_theme_mod( 'social_facebook', 'https://www.facebook.com/latvijasrokmuzikasasociacija' );
                $ig = get_theme_mod( 'social_instagram', 'https://www.instagram.com/latvian_rock_music_association/' );
                $yt = get_theme_mod( 'social_youtube',  'https://www.youtube.com/channel/UC21qJ1_LTB80UZlpL5LBOFA' );
                $tw = get_theme_mod( 'social_twitter' );
                if ( $fb ) : ?><a href="<?php echo esc_url( $fb ); ?>" class="social-link" target="_blank" rel="noopener">FB</a><?php endif; ?>
                <?php if ( $ig ) : ?><a href="<?php echo esc_url( $ig ); ?>" class="social-link" target="_blank" rel="noopener">IG</a><?php endif; ?>
                <?php if ( $yt ) : ?><a href="<?php echo esc_url( $yt ); ?>" class="social-link" target="_blank" rel="noopener">YT</a><?php endif; ?>
                <?php if ( $tw ) : ?><a href="<?php echo esc_url( $tw ); ?>" class="social-link" target="_blank" rel="noopener">TW</a><?php endif; ?>
            </div>
        </div>

        <div class="footer-col">
            <h4>Saturs</h4>
            <ul>
                <li><a href="<?php echo esc_url( home_url( '/category/jaunumi/' ) ); ?>">Raksti</a></li>
                <li><a href="<?php echo esc_url( home_url( '/category/intervijas/' ) ); ?>">Intervijas</a></li>
                <li><a href="<?php echo esc_url( home_url( '/category/festivali/' ) ); ?>">Festivāli</a></li>
                <li><a href="<?php echo esc_url( home_url( '/category/koncerti/' ) ); ?>">Koncerti</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>LRMA</h4>
            <ul>
                <li><a href="<?php echo esc_url( home_url( '/par-mums/' ) ); ?>">Par Mums</a></li>
                <li><a href="<?php echo esc_url( get_theme_mod( 'radio_url', 'https://rockradio.lv' ) ); ?>" target="_blank" rel="noopener">Rock Radio</a></li>
                <li><a href="<?php echo esc_url( home_url( '/biedri/' ) ); ?>">Kļūt par Biedru</a></li>
                <li><a href="mailto:<?php echo esc_attr( get_theme_mod( 'site_email', 'info@lrma.lv' ) ); ?>">Iesniegt Mūziku</a></li>
                <li><a href="<?php echo esc_url( home_url( '/kontakti/' ) ); ?>">Kontakti</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Platformas</h4>
            <ul>
                <li><a href="https://www.facebook.com/latvijasrokmuzikasasociacija" target="_blank" rel="noopener">Facebook</a></li>
                <li><a href="https://www.instagram.com/latvian_rock_music_association/" target="_blank" rel="noopener">Instagram</a></li>
                <li><a href="https://www.youtube.com/channel/UC21qJ1_LTB80UZlpL5LBOFA" target="_blank" rel="noopener">YouTube</a></li>
                <li><a href="https://groover.co/en/influencer/profile/0.lrma-latvian-rock-music-association/" target="_blank" rel="noopener">Groover</a></li>
            </ul>
        </div>

    </div><!-- .footer-top -->

    <div class="footer-bottom">
        <span>© <?php echo date( 'Y' ); ?> LRMA — Latvijas Rokmūzikas Asociācija</span>
        <span>Rīga, Latvija · lrma.lv</span>
    </div>

</footer>

<!-- Newsletter success popup -->
<div id="lrma-newsletter-popup" class="lrma-popup" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="lrma-popup-title">
    <div class="lrma-popup-box">
        <button class="lrma-popup-close" aria-label="Aizvērt">✕</button>
        <div class="lrma-popup-icon">✓</div>
        <div class="lrma-popup-title" id="lrma-popup-title">Paldies!</div>
        <div class="lrma-popup-message" id="lrma-popup-message"></div>
    </div>
</div>

<!-- SCROLL REVEAL + RADIO TOGGLE -->
<script>
(function() {
    // Scroll reveal
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(e) {
                if (e.isIntersecting) {
                    e.target.classList.add('revealed');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.08 });
        document.querySelectorAll('.reveal').forEach(function(el) { io.observe(el); });
    } else {
        document.querySelectorAll('.reveal').forEach(function(el) { el.classList.add('revealed'); });
    }

    // Radio play toggle (front-page player)
    var audio   = document.getElementById('radioAudio');
    var playBtn = document.getElementById('playBtn');

    function syncPlayBtn(playing) {
        if (!playBtn) return;
        playBtn.setAttribute('data-playing', playing ? '1' : '0');
        playBtn.innerHTML = playing
            ? '<svg viewBox="0 0 24 24"><rect x="6" y="4" width="4" height="16" fill="white"/><rect x="14" y="4" width="4" height="16" fill="white"/></svg>'
            : '<svg viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21" fill="white"/></svg>';
    }

    if (audio) {
        audio.addEventListener('play',  function() { syncPlayBtn(true);  });
        audio.addEventListener('pause', function() { syncPlayBtn(false); });
        audio.addEventListener('ended', function() { syncPlayBtn(false); });
        audio.addEventListener('error', function() {
            if (!playBtn) return;
            var err = document.createElement('span');
            err.textContent = 'Straume nav pieejama';
            err.style.cssText = 'display:block;font-size:11px;color:#cc2222;margin-top:6px;';
            var ctrl = playBtn.parentNode;
            ctrl.appendChild(err);
            setTimeout(function() { if (err.parentNode) err.parentNode.removeChild(err); }, 4000);
        });
    }

    window.lrmaTogglePlay = function() {
        if (!audio) return;
        if (audio.paused) { audio.play().catch(function() {}); }
        else              { audio.pause(); }
    };

    // Volume slider drag — wired to audio.volume
    var track = document.getElementById('volTrack');
    var fill  = document.getElementById('volFill');
    var thumb = document.getElementById('volThumb');
    if (track && fill && thumb) {
        // Default volume 80%
        if (audio) { audio.volume = 0.8; }
        fill.style.width = '80%';
        thumb.style.left = '80%';

        var dragging = false;
        function setVol(e) {
            var rect = track.getBoundingClientRect();
            var pct  = Math.min(1, Math.max(0, (e.clientX - rect.left) / rect.width));
            var p    = (pct * 100).toFixed(1) + '%';
            fill.style.width = p;
            thumb.style.left = p;
            if (audio) { audio.volume = pct; }
        }
        track.addEventListener('mousedown', function(e) { dragging = true; setVol(e); });
        document.addEventListener('mousemove', function(e) { if (dragging) setVol(e); });
        document.addEventListener('mouseup',   function()  { dragging = false; });
        track.addEventListener('touchstart', function(e) { setVol(e.touches[0]); }, { passive: true });
        track.addEventListener('touchmove',  function(e) { setVol(e.touches[0]); }, { passive: true });
    }
})();
</script>

<?php wp_footer(); ?>
</body>
</html>
