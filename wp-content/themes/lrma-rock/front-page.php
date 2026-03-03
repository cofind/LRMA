<?php get_header(); ?>

<div class="lrma-tagline-bar">
  <em>LRMA</em> — LRMA mērķis ir radīt, uzturēt un popularizēt ilgtspējīgu un starptautiski atzītu kultūras vidi Latvijā, popularizējot Latvijas rokmūziku.
</div>

<!-- GRAIN OVERLAY -->
<style>
body::after {
    content: '';
    position: fixed; inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
    pointer-events: none; z-index: 9998; opacity: 0.35;
}
</style>

<!-- ╔══════════════════════════════════════╗
     ║  HERO                               ║
     ╚══════════════════════════════════════╝ -->
<?php if ( get_theme_mod( 'show_hero', '1' ) ) : ?>
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-grid"></div>
    <div class="hero-eyebrow">Latvijas Rokmūzikas Asociācija · Dibināta 2016</div>
    <h1 class="hero-title">
        LATVIJAS<br>
        ROKS<br>
        <span class="hero-stroke">SKAN</span>
    </h1>
    <div class="hero-meta">
        <p class="hero-desc">Baltijas lielākais roka mūzikas medijs. Jaunumi, intervijas, recenzijas un tiešraide — 24/7 bez kompromisiem.</p>
        <div class="hero-actions">
            <a href="#jaunumi" class="btn btn-red">Jaunākie Raksti →</a>
            <a href="#radio" class="btn btn-outline">▶&nbsp;&nbsp;Klausīties Radio</a>
        </div>
    </div>
    <div class="hero-scroll">Ritināt</div>
</section>
<?php endif; ?>

<!-- ╔══════════════════════════════════════╗
     ║  HERO SLIDER                        ║
     ╚══════════════════════════════════════╝ -->
<?php get_template_part( 'template-parts/hero-slider' ); ?>

<!-- ╔══════════════════════════════════════╗
     ║  GENRE TICKER                       ║
     ╚══════════════════════════════════════╝ -->
<div class="genre-ticker">
    <div class="ticker-track">
        <?php
        $genres   = [ 'Heavy Metal', 'Hard Rock', 'Death Metal', 'Thrash Metal', 'Black Metal', 'Doom Metal', 'Alternatīvais Roks', 'Post-Punk', 'Indie Rock', 'Eksperimentālais', 'Noise Rock', 'Stoner Rock' ];
        $repeated = array_merge( $genres, $genres );
        foreach ( $repeated as $g ) {
            echo '<span class="ticker-item">' . esc_html( $g ) . '</span>';
        }
        ?>
    </div>
</div>

<!-- ╔══════════════════════════════════════╗
     ║  EDITORIAL ARTICLE GRID             ║
     ╚══════════════════════════════════════╝ -->
<?php
// Filter tabs
$filter_tabs = [
    0  => 'Visi',
    8  => 'Jaunumi',
    10 => 'Recenzijas',
    9  => 'Intervijas',
    12 => 'Festivāli',
];
$active_cat = isset( $_GET['cat'] ) ? (int) $_GET['cat'] : 0;
if ( ! array_key_exists( $active_cat, $filter_tabs ) ) {
    $active_cat = 0;
}

// Grid query — 5 posts (1 featured + 4 secondary)
$grid_args = [ 'posts_per_page' => 5, 'post_status' => 'publish' ];
if ( $active_cat > 0 ) $grid_args['cat'] = $active_cat;
$grid_query = new WP_Query( $grid_args );
$grid_posts = $grid_query->posts;
wp_reset_postdata();

// "Visi Raksti" link — Jaunumi category archive
$jaunumi_term = get_category_by_slug( 'jaunumi' );
$jaunumi_link = $jaunumi_term ? get_category_link( $jaunumi_term->term_id ) : home_url( '/category/jaunumi/' );
?>
<section id="jaunumi" class="editorial-section">

    <!-- Category filter tabs -->
    <div class="cat-filter-tabs">
        <?php foreach ( $filter_tabs as $cat_id => $label ) :
            $is_active = ( $cat_id === $active_cat );
            $tab_url   = $cat_id > 0
                ? esc_url( add_query_arg( 'cat', $cat_id, home_url( '/' ) ) )
                : esc_url( home_url( '/' ) );
        ?>
        <a href="<?php echo $tab_url; ?>"
           class="cat-tab<?php echo $is_active ? ' is-active' : ''; ?>"
           <?php echo $is_active ? 'aria-current="true"' : ''; ?>>
            <?php echo esc_html( $label ); ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Editorial grid: 1 large + 4 medium -->
    <?php if ( ! empty( $grid_posts ) ) : ?>
    <div class="editorial-grid reveal">
        <?php foreach ( $grid_posts as $i => $gpost ) :
            get_template_part( 'template-parts/card-article', null, [
                'post'    => $gpost,
                'variant' => ( $i === 0 ) ? 'large' : 'medium',
            ] );
        endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="section-footer">
        <a href="<?php echo esc_url( $jaunumi_link ); ?>" class="btn btn-outline">Visi Raksti &nbsp;→</a>
    </div>

</section>

<!-- ╔══════════════════════════════════════╗
     ║  JUMS VARĒTU PATIKT STRIP           ║
     ╚══════════════════════════════════════╝ -->
<?php
$shown_ids   = array_column( $grid_posts, 'ID' );
$strip_query = new WP_Query( [
    'posts_per_page' => 6,
    'post_status'    => 'publish',
    'post__not_in'   => $shown_ids ?: [ 0 ],
] );
$strip_posts = $strip_query->posts;
wp_reset_postdata();
?>
<?php if ( ! empty( $strip_posts ) ) : ?>
<section class="more-strip-section">

    <div class="more-strip-header">
        <div class="section-label">Jums Varētu Patikt</div>
        <a href="<?php echo esc_url( $jaunumi_link ); ?>" class="section-all-link">Visi Raksti &nbsp;→</a>
    </div>

    <div class="more-strip-wrap">
        <button class="strip-arrow strip-prev" aria-label="Iepriekšējais">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15,18 9,12 15,6"/></svg>
        </button>
        <div class="more-strip" id="moreStrip">
            <?php foreach ( $strip_posts as $spost ) :
                get_template_part( 'template-parts/card-article', null, [
                    'post'    => $spost,
                    'variant' => 'small',
                ] );
            endforeach; ?>
        </div>
        <button class="strip-arrow strip-next" aria-label="Nākamais">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9,6 15,12 9,18"/></svg>
        </button>
    </div>

    <script>
    (function () {
        var strip = document.getElementById('moreStrip');
        if (!strip) return;
        var cardW = 232;
        var prev  = strip.parentElement.querySelector('.strip-prev');
        var next  = strip.parentElement.querySelector('.strip-next');
        if (prev) prev.addEventListener('click', function () { strip.scrollBy({ left: -cardW * 2, behavior: 'smooth' }); });
        if (next) next.addEventListener('click', function () { strip.scrollBy({ left:  cardW * 2, behavior: 'smooth' }); });
    }());
    </script>

</section>
<?php endif; ?>

<!-- ╔══════════════════════════════════════╗
     ║  INTERVIEWS                         ║
     ╚══════════════════════════════════════╝ -->
<?php
$interviews = new WP_Query( [
    'posts_per_page' => 1,
    'category_name'  => 'intervijas',
    'post_status'    => 'publish',
] );
if ( $interviews->have_posts() ) : $interviews->the_post();
    $intv_cat = get_category_by_slug( 'intervijas' );
?>
<section class="interviews-section">

    <div class="interviews-header">
        <div>
            <div class="section-label">Jaunākā</div>
            <h2 class="section-title">Intervija</h2>
        </div>
        <?php if ( $intv_cat ) : ?>
        <a href="<?php echo esc_url( get_category_link( $intv_cat ) ); ?>" class="section-all-link">Visas Intervijas &nbsp;→</a>
        <?php endif; ?>
    </div>

    <a href="<?php the_permalink(); ?>" class="interview-card reveal">
        <div class="interview-card__img">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'large', [ 'class' => 'interview-card__photo' ] ); ?>
            <?php endif; ?>
        </div>
        <div class="interview-card__body">
            <div class="interview-card__kicker">
                <span class="card-tag">Intervija</span>
                <span class="interview-card__num">— 01</span>
            </div>
            <h3 class="interview-card__title"><?php the_title(); ?></h3>
            <p class="interview-card__excerpt"><?php echo wp_trim_words( get_the_excerpt(), 28, '…' ); ?></p>
            <div class="interview-card__meta">
                <span class="card-meta"><?php echo get_the_date( 'd.m.Y' ); ?> · <?php echo max( 1, ceil( str_word_count( get_the_content() ) / 200 ) ); ?> min lasīšana</span>
                <span class="interview-card__arrow">Lasīt &nbsp;→</span>
            </div>
        </div>
    </a>

</section>
<?php wp_reset_postdata(); endif; ?>

<?php
/* ── Mixcloud API helpers ─────────────────────────────────────────────
 * Fetches the latest cloudcast for a given Mixcloud username.
 * Cached in a transient for 6 h; falls back to $fallback on failure.
 * -------------------------------------------------------------------- */
function lrma_mc_latest( string $username, array $fallback ): array {
    $key    = 'lrma_mc_' . sanitize_key( $username );
    $cached = get_transient( $key );
    if ( $cached !== false ) {
        return $cached;
    }

    $api_url  = 'https://api.mixcloud.com/' . rawurlencode( $username ) . '/cloudcasts/?limit=1';
    $response = wp_remote_get( $api_url, [ 'timeout' => 6 ] );

    if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
        set_transient( $key, $fallback, 20 * MINUTE_IN_SECONDS );
        return $fallback;
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    $item = $body['data'][0] ?? null;

    if ( ! $item ) {
        set_transient( $key, $fallback, 20 * MINUTE_IN_SECONDS );
        return $fallback;
    }

    $result = [
        'url'   => $item['url'],
        'key'   => $item['key'],                          // e.g. /LRMA/episode-slug/
        'name'  => $item['name'],
        'thumb' => $item['pictures']['extra_large'] ?? $item['pictures']['large'] ?? '',
        'date'  => isset( $item['created_time'] ) ? date( 'd.m.Y', strtotime( $item['created_time'] ) ) : '',
    ];

    set_transient( $key, $result, 6 * HOUR_IN_SECONDS );
    return $result;
}

$pv = lrma_mc_latest( 'LRMA', [
    'url'   => 'https://www.mixcloud.com/LRMA/platais-vakars-p%C4%81vels-rutkovskis-2-da%C4%BCa-12012026/',
    'key'   => '/LRMA/platais-vakars-p%C4%81vels-rutkovskis-2-da%C4%BCa-12012026/',
    'name'  => 'Pāvels Rutkovskis — 2. Daļa',
    'thumb' => 'https://thumbnailer.mixcloud.com/unsafe/600x600/extaudio/e/5/e/8/27f5-f168-4b00-882d-9d4f19ccfadb',
    'date'  => '12.01.2026',
] );

$rn = lrma_mc_latest( 'radioswhrock', [
    'url'   => 'https://www.mixcloud.com/radioswhrock/roka-nemieri-25022026/',
    'key'   => '/radioswhrock/roka-nemieri-25022026/',
    'name'  => 'Roka Nemieri (25.02.2026)',
    'thumb' => 'https://thumbnailer.mixcloud.com/unsafe/600x600/extaudio/7/3/b/c/f2b2-affb-45c7-9fa6-09035c0ea5d6',
    'date'  => '25.02.2026',
] );

// Build widget iframe src from the cloudcast key.
function lrma_mc_widget_src( string $key ): string {
    return 'https://www.mixcloud.com/widget/iframe/?feed=' . rawurlencode( 'https://www.mixcloud.com' . $key ) . '&hide_cover=1&hide_artwork=0';
}
?>

<!-- ╔══════════════════════════════════════╗
     ║  PLATAIS VAKARS / MIXCLOUD          ║
     ╚══════════════════════════════════════╝ -->
<section id="radio" class="radio-section">
    <div class="radio-inner">

        <div class="radio-text reveal">
            <div class="section-label">Jaunākais Raidījums</div>
            <h2 class="section-title">Platais<br>Vakars</h2>
            <p class="radio-desc">Nedēļas roka mūzikas apskats ar aizraujošiem viesiem, dziļām diskusijām un labāko Latvijas roka mūziku. Jauns raidījums katru piektdienu.</p>
            <a href="https://www.mixcloud.com/LRMA/" target="_blank" rel="noopener" class="btn btn-outline" style="display:inline-flex;">Visi Raidījumi &nbsp;↗</a>
        </div><!-- .radio-text -->

        <div class="mc-card reveal">
            <a href="<?php echo esc_url( $pv['url'] ); ?>" target="_blank" rel="noopener" class="mc-thumb-wrap">
                <?php if ( $pv['thumb'] ) : ?>
                <img src="<?php echo esc_url( $pv['thumb'] ); ?>"
                     alt="<?php echo esc_attr( 'Platais Vakars — ' . $pv['name'] ); ?>"
                     class="mc-thumb-img">
                <?php endif; ?>
                <div class="mc-thumb-overlay">
                    <svg class="mc-play-icon" viewBox="0 0 24 24" fill="white"><polygon points="5,3 19,12 5,21"/></svg>
                </div>
            </a>
            <div class="mc-body">
                <div class="mc-meta">
                    <span class="card-tag">Platais Vakars</span>
                    <?php if ( $pv['date'] ) : ?><span class="mc-date"><?php echo esc_html( $pv['date'] ); ?></span><?php endif; ?>
                </div>
                <div class="mc-title"><?php echo esc_html( $pv['name'] ); ?></div>
            </div>
            <div class="mc-player">
                <iframe width="100%" height="120"
                    src="<?php echo esc_url( lrma_mc_widget_src( $pv['key'] ) ); ?>"
                    frameborder="0"
                    allow="encrypted-media; fullscreen; autoplay; idle-detection; speaker-selection; web-share;"
                    loading="lazy"></iframe>
            </div>
        </div><!-- .mc-card -->

    </div><!-- .radio-inner -->
</section>

<!-- ╔══════════════════════════════════════╗
     ║  ROKA NEMIERI / MIXCLOUD            ║
     ╚══════════════════════════════════════╝ -->
<section class="radio-section radio-section--alt">
    <div class="radio-inner radio-inner--flip">

        <div class="mc-card reveal">
            <a href="<?php echo esc_url( $rn['url'] ); ?>" target="_blank" rel="noopener" class="mc-thumb-wrap">
                <?php if ( $rn['thumb'] ) : ?>
                <img src="<?php echo esc_url( $rn['thumb'] ); ?>"
                     alt="<?php echo esc_attr( 'Roka Nemieri — ' . $rn['name'] ); ?>"
                     class="mc-thumb-img">
                <?php endif; ?>
                <div class="mc-thumb-overlay">
                    <svg class="mc-play-icon" viewBox="0 0 24 24" fill="white"><polygon points="5,3 19,12 5,21"/></svg>
                </div>
            </a>
            <div class="mc-body">
                <div class="mc-meta">
                    <span class="card-tag">Roka Nemieri</span>
                    <?php if ( $rn['date'] ) : ?><span class="mc-date"><?php echo esc_html( $rn['date'] ); ?></span><?php endif; ?>
                </div>
                <div class="mc-title"><?php echo esc_html( $rn['name'] ); ?></div>
            </div>
            <div class="mc-player">
                <iframe width="100%" height="120"
                    src="<?php echo esc_url( lrma_mc_widget_src( $rn['key'] ) ); ?>"
                    frameborder="0"
                    allow="encrypted-media; fullscreen; autoplay; idle-detection; speaker-selection; web-share;"
                    loading="lazy"></iframe>
            </div>
        </div><!-- .mc-card -->

        <div class="radio-text reveal">
            <div class="section-label">Jaunākais Raidījums</div>
            <h2 class="section-title">Roka<br>Nemieri</h2>
            <p class="radio-desc">Latvijas roka mūzikas nedēļas kopsavilkums ēterā Radio SWH Rock. Koncerti, albumi, intervijas — viss, kas notiek Latvijas roka ainā katru nedēļu.</p>
            <a href="https://www.mixcloud.com/radioswhrock/" target="_blank" rel="noopener" class="btn btn-outline" style="display:inline-flex;">Visi Raidījumi &nbsp;↗</a>
        </div><!-- .radio-text -->

    </div><!-- .radio-inner -->
</section>

<!-- ╔══════════════════════════════════════╗
     ║  CONCERTS                           ║
     ╚══════════════════════════════════════╝ -->
<?php
/* ── Fetch upcoming concerts from concerts-metal.com ─────────────────
 * Cached in a transient for 12 h. Falls back to hardcoded data if the
 * remote request fails or Cloudflare blocks it.
 * ------------------------------------------------------------------- */
function lrma_fetch_cm_concerts( int $limit = 5 ): array {
    $key    = 'lrma_cm_latvia_v1';
    $cached = get_transient( $key );
    if ( $cached !== false ) {
        return $cached;
    }

    $source_url = 'https://en.concerts-metal.com/LV__Latvia';
    $response   = wp_remote_get( $source_url, [
        'timeout'    => 8,
        'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
        'headers'    => [
            'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Referer'         => 'https://en.concerts-metal.com/',
        ],
    ] );

    if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
        set_transient( $key, [], 20 * MINUTE_IN_SECONDS );
        return [];
    }

    $html = wp_remote_retrieve_body( $response );

    // Bail if we got a Cloudflare challenge instead of real content.
    if ( str_contains( $html, 'cf-turnstile' ) || str_contains( $html, 'challenge-platform' ) ) {
        set_transient( $key, [], 20 * MINUTE_IN_SECONDS );
        return [];
    }

    /* ── Parse ── */
    $dom = new DOMDocument();
    libxml_use_internal_errors( true );
    $dom->loadHTML( '<?xml encoding="UTF-8">' . $html );
    libxml_clear_errors();
    $xpath = new DOMXPath( $dom );

    // Latvian month abbreviations keyed by PHP 'M' output.
    $lv_months = [
        'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr',
        'May' => 'Mai', 'Jun' => 'Jūn', 'Jul' => 'Jūl', 'Aug' => 'Aug',
        'Sep' => 'Sep', 'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Dec',
    ];

    $today   = date( 'Y-m-d' );
    $results = [];

    foreach ( $xpath->query( '//tr' ) as $row ) {
        if ( count( $results ) >= $limit ) break;

        $tds = $row->getElementsByTagName( 'td' );
        if ( $tds->length < 2 ) continue;

        // Date is expected in the first <td> as DD/MM/YYYY.
        $raw_date = trim( $tds->item(0)->textContent );
        if ( ! preg_match( '#(\d{2})/(\d{2})/(\d{4})#', $raw_date, $m ) ) continue;

        $date_iso = "{$m[3]}-{$m[2]}-{$m[1]}";
        if ( $date_iso < $today ) continue; // skip past events

        // Artist / event name + individual link from second <td>.
        $td_artist   = $tds->item(1);
        $artist_name = trim( $td_artist->textContent );
        $event_url   = $source_url; // default to Latvia index

        $anchors = $td_artist->getElementsByTagName( 'a' );
        if ( $anchors->length > 0 ) {
            $href = trim( $anchors->item(0)->getAttribute( 'href' ) );
            if ( $href ) {
                $event_url = str_starts_with( $href, 'http' )
                    ? $href
                    : 'https://en.concerts-metal.com' . $href;
            }
        }

        // Venue / city from the remaining <td> cells.
        $venue_parts = [];
        for ( $i = 2; $i < min( $tds->length, 4 ); $i++ ) {
            $t = trim( $tds->item( $i )->textContent );
            if ( $t !== '' ) $venue_parts[] = $t;
        }
        $venue = implode( ', ', $venue_parts );

        $en_month = date( 'M', mktime( 0, 0, 0, (int) $m[2], 1, (int) $m[3] ) );

        $results[] = [
            'day'   => $m[1],
            'month' => $lv_months[ $en_month ] ?? $en_month,
            'name'  => $artist_name,
            'venue' => $venue,
            'url'   => $event_url,
        ];
    }

    set_transient( $key, $results, $results ? 12 * HOUR_IN_SECONDS : 20 * MINUTE_IN_SECONDS );
    return $results;
}

/* ── Priority 1: custom CPT posts ─────────────────────────────────── */
$cpt_concerts = new WP_Query( [
    'post_type'      => 'koncerti',
    'posts_per_page' => 5,
    'orderby'        => 'meta_value',
    'meta_key'       => 'concert_date',
    'order'          => 'ASC',
    'post_status'    => 'publish',
] );

/* ── Priority 2: live feed ────────────────────────────────────────── */
$live_concerts = $cpt_concerts->have_posts() ? [] : lrma_fetch_cm_concerts( 5 );

/* ── Priority 3: hardcoded fallback ──────────────────────────────── */
$fallback_concerts = [
    [ 'day' => '07', 'month' => 'Apr', 'name' => 'Thrown – Tour 2026',                    'venue' => 'Angars Concert Hall, Rīga', 'url' => 'https://en.concerts-metal.com/LV__Latvia' ],
    [ 'day' => '02', 'month' => 'Mai', 'name' => 'The 69 Eyes – I Survive Tour 2026',     'venue' => 'Melnā Piektdiena, Rīga',    'url' => 'https://en.concerts-metal.com/LV__Latvia' ],
    [ 'day' => '28', 'month' => 'Mai', 'name' => 'Laibach',                               'venue' => 'Spelet, Rīga',              'url' => 'https://en.concerts-metal.com/LV__Latvia' ],
    [ 'day' => '16', 'month' => 'Jūn', 'name' => 'Bury Tomorrow – Tour 2026',             'venue' => 'Melnā Piektdiena, Rīga',    'url' => 'https://en.concerts-metal.com/LV__Latvia' ],
    [ 'day' => '29', 'month' => 'Jūn', 'name' => 'Blood Incantation – Absolute Elsewhere', 'venue' => 'Spelet, Rīga',             'url' => 'https://en.concerts-metal.com/LV__Latvia' ],
];
?>
<section id="koncerti" class="concerts-section">
    <div class="section-label">Gaidāmie</div>
    <h2 class="section-title">Koncerti &amp; Festivāli</h2>

    <div class="concert-list reveal">
    <?php if ( $cpt_concerts->have_posts() ) :
        /* ── CPT posts ── */
        while ( $cpt_concerts->have_posts() ) : $cpt_concerts->the_post();
            $date_raw   = get_post_meta( get_the_ID(), 'concert_date', true );
            $venue      = get_post_meta( get_the_ID(), 'concert_venue', true );
            $ticket_url = get_post_meta( get_the_ID(), 'concert_ticket_url', true );
            $day        = $date_raw ? date( 'd', strtotime( $date_raw ) ) : '—';
            $month_en   = $date_raw ? date( 'M', strtotime( $date_raw ) ) : '—';
            $lv_map     = [ 'Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'Mai','Jun'=>'Jūn','Jul'=>'Jūl','Aug'=>'Aug','Sep'=>'Sep','Oct'=>'Okt','Nov'=>'Nov','Dec'=>'Dec' ];
            $month      = $lv_map[ $month_en ] ?? $month_en;
    ?>
        <div class="concert-row">
            <div class="concert-date">
                <span class="concert-day"><?php echo esc_html( $day ); ?></span>
                <span class="concert-month"><?php echo esc_html( $month ); ?></span>
            </div>
            <div class="concert-info">
                <div class="concert-name"><?php the_title(); ?></div>
                <?php if ( $venue ) : ?><div class="concert-venue"><?php echo esc_html( $venue ); ?></div><?php endif; ?>
            </div>
            <div class="concert-ticket">
                <?php if ( $ticket_url ) : ?>
                    <a href="<?php echo esc_url( $ticket_url ); ?>" target="_blank" rel="noopener" class="ticket-btn">Biļetes</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; wp_reset_postdata();

    else :
        /* ── Live feed or fallback ── */
        $display = $live_concerts ?: $fallback_concerts;
        foreach ( $display as $c ) : ?>
        <div class="concert-row">
            <div class="concert-date">
                <span class="concert-day"><?php echo esc_html( $c['day'] ); ?></span>
                <span class="concert-month"><?php echo esc_html( $c['month'] ); ?></span>
            </div>
            <div class="concert-info">
                <div class="concert-name"><?php echo esc_html( $c['name'] ); ?></div>
                <?php if ( $c['venue'] ) : ?><div class="concert-venue"><?php echo esc_html( $c['venue'] ); ?></div><?php endif; ?>
            </div>
            <div class="concert-ticket">
                <a href="<?php echo esc_url( $c['url'] ); ?>" target="_blank" rel="noopener" class="ticket-btn">Info ↗</a>
            </div>
        </div>
        <?php endforeach;
    endif; ?>
    </div><!-- .concert-list -->

    <div class="section-footer">
        <a href="https://en.concerts-metal.com/LV__Latvia" target="_blank" rel="noopener" class="btn btn-outline">
            Visi Koncerti Latvijā &nbsp;↗
        </a>
    </div>

</section>

<!-- ╔══════════════════════════════════════╗
     ║  STATS STRIP                        ║
     ╚══════════════════════════════════════╝ -->
<div class="stats-strip reveal">
    <div class="stat-block">
        <span class="stat-num">7<em>+</em></span>
        <span class="stat-lbl">Gadi Aktīvi</span>
    </div>
    <div class="stat-block">
        <span class="stat-num">10K<em>+</em></span>
        <span class="stat-lbl">Sekotāji</span>
    </div>
    <div class="stat-block">
        <span class="stat-num">24<em>/7</em></span>
        <span class="stat-lbl">Radio Tiešraide</span>
    </div>
    <div class="stat-block">
        <span class="stat-num">500<em>+</em></span>
        <span class="stat-lbl">Publicēti Raksti</span>
    </div>
</div>

<?php get_footer(); ?>
