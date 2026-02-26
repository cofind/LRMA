<?php
/* ── Logo detection ──────────────────────────────────────────────── */
$logo_url = '';

// 1. WordPress Customizer custom_logo
$logo_id = get_theme_mod( 'custom_logo' );
if ( $logo_id ) {
    $logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
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

// Dark-logo detection (invert if filename contains 'black' or 'dark')
$logo_classes = 'lrma-logo-img';
if ( ! empty( $logo_url ) && ( strpos( $logo_url, 'black' ) !== false || strpos( $logo_url, 'dark' ) !== false ) ) {
    $logo_classes .= ' dark-logo';
}

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

  <!-- ── ROW 1: RED TOPBAR ─────────────────────────────────────── -->
  <div class="lrma-topbar">
    <div class="topbar-ticker-wrap">
      <div class="topbar-ticker">
        <?php
        $ticker_posts = new WP_Query( [ 'posts_per_page' => 6, 'post_status' => 'publish' ] );
        $ticker_items = [];
        if ( $ticker_posts->have_posts() ) {
            while ( $ticker_posts->have_posts() ) {
                $ticker_posts->the_post();
                $ticker_items[] = '<a href="' . esc_url( get_permalink() ) . '" class="topbar-item"><span class="topbar-dot"></span>' . esc_html( get_the_title() ) . '</a>';
            }
            wp_reset_postdata();
        }
        $all_items = array_merge( $ticker_items, $ticker_items );
        echo implode( '', $all_items );
        ?>
      </div>
    </div>
    <div class="topbar-right">
      <a href="<?php echo esc_url( home_url( '/en/' ) ); ?>" class="topbar-link">EN</a>
      <span class="topbar-sep">·</span>
      <a href="mailto:<?php echo esc_attr( get_theme_mod( 'site_email', 'info@lrma.lv' ) ); ?>" class="topbar-link">
        <?php echo esc_html( get_theme_mod( 'site_email', 'info@lrma.lv' ) ); ?>
      </a>
    </div>
  </div>

  <!-- ── ROW 2: MAIN NAV ───────────────────────────────────────── -->
  <div class="lrma-mainrow">

    <!-- LOGO -->
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="lrma-logo" aria-label="<?php bloginfo( 'name' ); ?>">
      <?php if ( ! empty( $logo_url ) ) : ?>
        <img src="<?php echo esc_url( $logo_url ); ?>"
             alt="<?php bloginfo( 'name' ); ?>"
             class="<?php echo esc_attr( $logo_classes ); ?>"
             width="120" height="40">
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
      <a href="mailto:<?php echo esc_attr( get_theme_mod( 'site_email', 'info@lrma.lv' ) ); ?>" class="lrma-cta">
        Iesniegt Mūziku
      </a>
    </div>

    <!-- MOBILE BURGER -->
    <button class="lrma-burger" aria-label="Atvērt izvēlni"
            onclick="document.getElementById('lrma-mobile-nav').classList.toggle('open');this.classList.toggle('active')">
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

  <!-- ── MOBILE NAV OVERLAY ─────────────────────────────────────── -->
  <div id="lrma-mobile-nav" class="lrma-mobile-nav">
    <?php
    wp_nav_menu( [
      'theme_location'  => 'primary',
      'menu_class'      => 'lrma-mobile-menu',
      'container'       => false,
      'depth'           => 1,
      'fallback_cb'     => false,
    ] );
    ?>
    <div class="mobile-nav-tagline">
      LRMA mērķis ir radīt, uzturēt un popularizēt ilgtspējīgu un starptautiski atzītu kultūras vidi Latvijā, popularizējot Latvijas rokmūziku.
    </div>
  </div>

</header>

<?php if ( is_front_page() || is_home() ) : ?>
<div class="lrma-tagline-bar">
  <em>LRMA</em> — LRMA mērķis ir radīt, uzturēt un popularizēt ilgtspējīgu un starptautiski atzītu kultūras vidi Latvijā, popularizējot Latvijas rokmūziku.
</div>
<?php endif; ?>

<script>
(function () {
  var header  = document.getElementById('lrma-header');
  var THRESHOLD = 10;

  function onScroll() {
    if (window.scrollY > THRESHOLD) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
})();
</script>
