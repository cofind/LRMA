<?php
/* ── Logo detection ──────────────────────────────────────────────── */
$logo_url = '';

// 1. WordPress Customizer custom_logo
$logo_id = get_theme_mod( 'custom_logo' );
if ( $logo_id ) {
    $logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
    // Append attachment ID as cache-buster: URL changes whenever the logo is swapped.
    if ( $logo_url ) {
        $logo_url = add_query_arg( 'v', $logo_id, $logo_url );
    }
}

// 2. Theme directory scan
if ( empty( $logo_url ) ) {
    $candidates = [
        get_template_directory() . '/assets/img/logo.png',
        get_template_directory() . '/assets/img/logo.svg',
        get_template_directory() . '/assets/images/logo.png',
        get_template_directory() . '/images/logo.png',
        get_template_directory() . '/img/logo.png',
    ];
    foreach ( $candidates as $path ) {
        if ( file_exists( $path ) ) {
            $logo_url = get_template_directory_uri() . str_replace( get_template_directory(), '', $path );
            break;
        }
    }
    if ( empty( $logo_url ) ) {
        $glob_matches = glob( get_template_directory() . '/assets/img/logo.*' );
        if ( ! empty( $glob_matches ) ) {
            $logo_url = get_template_directory_uri() . '/assets/img/' . basename( $glob_matches[0] );
        }
    }
}

// 3. Search uploads for a logo attachment
if ( empty( $logo_url ) ) {
    $args = [
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        's'              => 'logo',
        'posts_per_page' => 1,
    ];
    $q = new WP_Query( $args );
    if ( $q->have_posts() ) {
        $logo_url = wp_get_attachment_url( $q->posts[0]->ID );
    }
    wp_reset_postdata();
}

$logo_classes = 'lrma-logo-img';

/* ── Radio config ────────────────────────────────────────────────── */
$stream_url = esc_url_raw( get_theme_mod( 'radio_stream_url', 'https://c22.radioboss.fm/stream/318' ) );
$meta_url   = esc_url_raw( get_theme_mod( 'radio_meta_url',   'https://c22.radioboss.fm/w/nowplayinginfo?u=318' ) );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="lrma-header">

  <!-- ── MAIN NAV ──────────────────────────────────────────────── -->
  <div class="lrma-mainrow">

    <!-- LOGO -->
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="lrma-logo" aria-label="<?php bloginfo( 'name' ); ?>">
      <?php if ( ! empty( $logo_url ) ) : ?>
        <img src="<?php echo esc_url( $logo_url ); ?>"
             alt="<?php bloginfo( 'name' ); ?>"
             class="<?php echo esc_attr( $logo_classes ); ?>">
      <?php else : ?>
        <span class="lrma-logo-text">LR<em>M</em>A</span>
      <?php endif; ?>
    </a>

    <!-- PRIMARY NAV -->
    <nav class="lrma-nav" aria-label="Galvenā navigācija">
      <?php
      wp_nav_menu( [
        'theme_location'  => 'primary',
        'menu_class'      => 'lrma-nav-list',
        'container'       => false,
        'depth'           => 1,
        'fallback_cb'     => false,
        'link_before'     => '',
        'link_after'      => '',
      ] );
      ?>
    </nav>

    <!-- RIGHT ACTIONS -->
    <div class="lrma-nav-actions">
      <button class="lrma-search-btn" aria-label="Meklēt"
              onclick="document.getElementById('lrma-search').classList.toggle('open')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
      </button>
      <div class="header-radio-pill">
        <button class="header-radio-btn" id="headerPlayBtn" aria-label="Atskaņot radio">
          <svg class="radio-play-icon" width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
            <polygon points="5 3 19 12 5 21 5 3"/>
          </svg>
          <svg class="radio-pause-icon" width="12" height="12" viewBox="0 0 24 24" fill="currentColor" style="display:none">
            <rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/>
          </svg>
        </button>
        <div class="header-radio-wave">
          <span></span><span></span><span></span><span></span><span></span>
        </div>
        <span class="header-radio-track" id="headerRadioTrack">Rock Radio Latvia</span>
      </div>
      <?php if ( function_exists( 'pll_the_languages' ) ) : ?>
      <div class="lang-switcher" role="navigation" aria-label="Valodas izvēle">
        <?php
        $pll_langs = pll_the_languages( [ 'raw' => 1 ] );
        foreach ( array_keys( $pll_langs ) as $slug ) {
            $l   = $pll_langs[ $slug ];
            $cls = $l['current_lang'] ? 'current-lang' : '';
            echo '<a href="' . esc_url( $l['url'] ) . '" class="' . esc_attr( $cls ) . '" hreflang="' . esc_attr( $l['slug'] ) . '">' . strtoupper( esc_html( $l['slug'] ) ) . '</a>';
        }
        ?>
      </div>
      <?php endif; ?>
      <a href="mailto:<?php echo esc_attr( get_theme_mod( 'site_email', 'info@lrma.lv' ) ); ?>" class="lrma-cta">
        Iesniegt Mūziku
      </a>
    </div>

    <!-- MOBILE BURGER -->
    <button class="lrma-burger" id="mobileMenuBurger"
            aria-label="Atvērt izvēlni" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>

  </div>

  <!-- ── SEARCH BAR ─────────────────────────────────────────────── -->
  <div id="lrma-search" class="lrma-search-bar">
    <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
      <input type="search" name="s"
             placeholder="Meklēt rakstus, mākslinieku, grupu..."
             value="<?php echo get_search_query(); ?>"
             autocomplete="off">
      <button type="submit">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
      </button>
    </form>
    <button class="search-close"
            onclick="document.getElementById('lrma-search').classList.remove('open')"
            aria-label="Aizvērt">✕</button>
  </div>


</header>

<!-- ── AUDIO SINGLETON (shared by all players) ────────────────────── -->
<audio id="radioAudio" preload="none"
       src="<?php echo esc_url( $stream_url ); ?>"></audio>

<!-- ── FULL-SCREEN MOBILE MENU ────────────────────────────────────── -->
<div id="mobileMenu" role="dialog" aria-modal="true"
     aria-label="Navigācija" aria-hidden="true">

  <button class="mobile-menu-close" id="mobileMenuClose" aria-label="Aizvērt izvēlni">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="2" stroke-linecap="round">
      <path d="M18 6 6 18M6 6l12 12"/>
    </svg>
  </button>

  <nav class="mobile-menu-nav" aria-label="Galvenā navigācija">
    <?php
    wp_nav_menu( [
      'theme_location' => 'primary',
      'menu_class'     => 'mobile-menu-list',
      'container'      => false,
      'depth'          => 1,
      'fallback_cb'    => false,
    ] );
    ?>
  </nav>

  <div class="mobile-menu-utils">
    <?php if ( function_exists( 'pll_the_languages' ) ) : ?>
    <div class="mobile-lang-switcher">
      <?php
      $pll_m = pll_the_languages( [ 'raw' => 1 ] );
      foreach ( array_keys( $pll_m ) as $slug ) {
          $l   = $pll_m[ $slug ];
          $cls = 'mobile-lang-btn' . ( $l['current_lang'] ? ' current-lang' : '' );
          echo '<a href="' . esc_url( $l['url'] ) . '" class="' . esc_attr( $cls ) . '" hreflang="' . esc_attr( $l['slug'] ) . '">' . strtoupper( esc_html( $l['slug'] ) ) . '</a>';
      }
      ?>
    </div>
    <?php endif; ?>
    <a href="mailto:<?php echo esc_attr( get_theme_mod( 'site_email', 'info@lrma.lv' ) ); ?>"
       class="mobile-util-link">
      <?php echo esc_html( get_theme_mod( 'site_email', 'info@lrma.lv' ) ); ?>
    </a>
  </div>

  <div class="mobile-radio-player">
    <button class="mobile-radio-btn" id="mobileRadioBtn" aria-label="Atskaņot radio">
      <span class="radio-live-dot"></span>
      <svg class="radio-play-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
        <polygon points="5 3 19 12 5 21 5 3"/>
      </svg>
      <svg class="radio-pause-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
        <rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/>
      </svg>
    </button>
    <div class="mobile-radio-info">
      <span class="mobile-radio-label">LIVE · ROCK RADIO</span>
      <span class="mobile-radio-track" id="mobileRadioTrack">Rock Radio Latvia</span>
    </div>
  </div>

</div>

<script>
(function () {
  /* ── Scroll: frosted-glass header ─────────────────────────────── */
  var header = document.getElementById('lrma-header');
  function onScroll() {
    header.classList.toggle('scrolled', window.scrollY > 10);
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ── Mobile menu ───────────────────────────────────────────────── */
  var menu    = document.getElementById('mobileMenu');
  var burger  = document.getElementById('mobileMenuBurger');
  var closeBtn = document.getElementById('mobileMenuClose');

  function openMenu() {
    menu.classList.add('open');
    burger.classList.add('active');
    burger.setAttribute('aria-expanded', 'true');
    menu.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    closeBtn.focus();
  }
  function closeMenu() {
    menu.classList.remove('open');
    burger.classList.remove('active');
    burger.setAttribute('aria-expanded', 'false');
    menu.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    burger.focus();
  }

  burger.addEventListener('click', function () {
    menu.classList.contains('open') ? closeMenu() : openMenu();
  });
  closeBtn.addEventListener('click', closeMenu);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && menu.classList.contains('open')) closeMenu();
  });
  menu.querySelectorAll('a').forEach(function (a) {
    a.addEventListener('click', closeMenu);
  });

  /* ── Radio mini-player (shared) ───────────────────────────────── */
  var audio         = document.getElementById('radioAudio');
  var mobileBtn     = document.getElementById('mobileRadioBtn');
  var trackEl       = document.getElementById('mobileRadioTrack');
  var playIcon      = mobileBtn.querySelector('.radio-play-icon');
  var pauseIcon     = mobileBtn.querySelector('.radio-pause-icon');
  var headerBtn     = document.getElementById('headerPlayBtn');
  var headerTrackEl = document.getElementById('headerRadioTrack');
  var headerPill    = document.querySelector('.header-radio-pill');
  var hPlayIcon     = headerBtn ? headerBtn.querySelector('.radio-play-icon')  : null;
  var hPauseIcon    = headerBtn ? headerBtn.querySelector('.radio-pause-icon') : null;

  function syncRadioUI() {
    var playing = !audio.paused;
    playIcon.style.display  = playing ? 'none' : '';
    pauseIcon.style.display = playing ? '' : 'none';
    mobileBtn.setAttribute('aria-label', playing ? 'Apturēt radio' : 'Atskaņot radio');
    mobileBtn.classList.toggle('playing', playing);
    if (hPlayIcon)  hPlayIcon.style.display  = playing ? 'none' : '';
    if (hPauseIcon) hPauseIcon.style.display = playing ? '' : 'none';
    if (headerBtn)  headerBtn.setAttribute('aria-label', playing ? 'Apturēt radio' : 'Atskaņot radio');
    if (headerBtn)  headerBtn.classList.toggle('playing', playing);
    if (headerPill) headerPill.classList.toggle('playing', playing);
  }
  audio.addEventListener('play',  syncRadioUI);
  audio.addEventListener('pause', syncRadioUI);
  audio.addEventListener('ended', syncRadioUI);
  syncRadioUI();

  mobileBtn.addEventListener('click', function () {
    if (audio.paused) { audio.play().catch(function () {}); }
    else              { audio.pause(); }
  });
  if (headerBtn) {
    headerBtn.addEventListener('click', function () {
      if (audio.paused) { audio.play().catch(function () {}); }
      else              { audio.pause(); }
    });
  }

  /* Track metadata polling */
  var META_URL = '<?php echo esc_js( $meta_url ); ?>';
  function fetchTrack() {
    if (!META_URL) return;
    fetch(META_URL)
      .then(function (r) { return r.json(); })
      .then(function (d) {
        var t = d && (d.nowplaying || d.currenttrack || d.autodj_title || d.track || d.title) || '';
        if (t && trackEl)       trackEl.textContent       = t;
        if (t && headerTrackEl) headerTrackEl.textContent = t;
      })
      .catch(function () {});
  }
  fetchTrack();
  setInterval(fetchTrack, 30000);
})();
</script>

<main id="lrma-main">
