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
     ║  RAKSTI — TABBED EDITORIAL GRID     ║
     ╚══════════════════════════════════════╝ -->
<?php
// Pre-fetch all tab content server-side — JS toggles visibility, no page reload
$raksti_tabs = [
    'visi'      => get_posts( [ 'posts_per_page' => 9, 'post_status' => 'publish',
                      'category_name' => 'jaunumi,intervijas,festivali,koncerti',
                      'orderby' => 'date', 'order' => 'DESC' ] ),
    'jaunumi'   => get_posts( [ 'posts_per_page' => 9, 'post_status' => 'publish',
                      'category_name' => 'jaunumi', 'orderby' => 'date', 'order' => 'DESC' ] ),
    'intervijas'=> get_posts( [ 'posts_per_page' => 9, 'post_status' => 'publish',
                      'category_name' => 'intervijas', 'orderby' => 'date', 'order' => 'DESC' ] ),
    'fk'        => get_posts( [ 'posts_per_page' => 9, 'post_status' => 'publish',
                      'category_name' => 'festivali,koncerti', 'orderby' => 'date', 'order' => 'DESC' ] ),
];
$raksti_labels = [
    'visi'       => 'Visi',
    'jaunumi'    => 'Jaunumi',
    'intervijas' => 'Intervijas',
    'fk'         => 'Festivāli / Koncerti',
];
$raksti_links = [
    'visi'       => home_url( '/category/jaunumi/' ),
    'jaunumi'    => home_url( '/category/jaunumi/' ),
    'intervijas' => home_url( '/category/intervijas/' ),
    'fk'         => home_url( '/category/koncerti/' ),
];
// Collect all shown IDs for the strip below
$grid_posts = $raksti_tabs['visi'];
?>
<section id="raksti" class="editorial-section">

    <div class="editorial-section-header">
        <div class="section-label">Raksti</div>
        <h2 class="section-title" style="margin:0;">Raksti</h2>
    </div>

    <!-- Tab bar — client-side switching -->
    <div class="lrma-tabs" role="tablist">
        <?php foreach ( $raksti_labels as $key => $label ) : ?>
        <button
            class="lrma-tab<?php echo $key === 'visi' ? ' is-active' : ''; ?>"
            data-tab="<?php echo esc_attr( $key ); ?>"
            role="tab"
            aria-selected="<?php echo $key === 'visi' ? 'true' : 'false'; ?>"
            aria-controls="lrma-tab-<?php echo esc_attr( $key ); ?>"
        ><?php echo esc_html( $label ); ?></button>
        <?php endforeach; ?>
    </div>

    <!-- Tab panels — all rendered, hidden until active -->
    <?php foreach ( $raksti_tabs as $key => $tab_posts ) : ?>
    <div
        id="lrma-tab-<?php echo esc_attr( $key ); ?>"
        class="lrma-tab-panel<?php echo $key === 'visi' ? ' is-active' : ''; ?>"
        role="tabpanel"
        aria-labelledby="lrma-tab-btn-<?php echo esc_attr( $key ); ?>"
    >
        <?php if ( ! empty( $tab_posts ) ) : ?>
        <div class="archive-grid reveal">
            <?php foreach ( $tab_posts as $tab_post ) :
                get_template_part( 'template-parts/card-article', null, [ 'post' => $tab_post ] );
            endforeach; ?>
        </div>
        <?php else : ?>
        <div class="archive-empty"><div class="archive-empty__label">Nav rakstu šajā kategorijā.</div></div>
        <?php endif; ?>

        <div class="lrma-tab-footer">
            <a href="<?php echo esc_url( $raksti_links[ $key ] ); ?>" class="btn btn-outline">
                Visi <?php echo $key === 'visi' ? 'raksti' : esc_html( strtolower( $raksti_labels[ $key ] ) ); ?> &nbsp;→
            </a>
        </div>
    </div>
    <?php endforeach; ?>

</section>

<!-- ╔══════════════════════════════════════╗
     ║  JUMS VARĒTU PATIKT STRIP           ║
     ╚══════════════════════════════════════╝ -->
<?php
$shown_ids = array_column( $grid_posts, 'ID' );
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

$pv_fallback_url = get_theme_mod( 'lrma_platais_vakars_url', 'https://www.mixcloud.com/LRMA/platais-vakars-p%C4%81vels-rutkovskis-2-da%C4%BCa-12012026/' );
$pv = lrma_mc_latest( 'LRMA', [
    'url'   => $pv_fallback_url,
    'key'   => wp_parse_url( $pv_fallback_url, PHP_URL_PATH ) ?: '/LRMA/',
    'name'  => get_theme_mod( 'lrma_platais_vakars_title', 'PLATAIS VAKARS Pāvels Rutkovskis 2. daļa' ),
    'thumb' => get_theme_mod( 'lrma_platais_vakars_thumb', 'https://thumbnailer.mixcloud.com/unsafe/600x600/extaudio/e/5/e/8/27f5-f168-4b00-882d-9d4f19ccfadb' ),
    'date'  => '',
] );

$rn_fallback_url = get_theme_mod( 'lrma_roka_nemieri_url', 'https://www.mixcloud.com/radioswhrock/roka-nemieri-25022026/' );
$rn = lrma_mc_latest( 'radioswhrock', [
    'url'   => $rn_fallback_url,
    'key'   => wp_parse_url( $rn_fallback_url, PHP_URL_PATH ) ?: '/radioswhrock/',
    'name'  => get_theme_mod( 'lrma_roka_nemieri_title', 'Roka Nemieri (25.02.2026)' ),
    'thumb' => get_theme_mod( 'lrma_roka_nemieri_thumb', 'https://thumbnailer.mixcloud.com/unsafe/600x600/extaudio/7/3/b/c/f2b2-affb-45c7-9fa6-09035c0ea5d6' ),
    'date'  => '',
] );

// Build widget iframe src from the cloudcast key.
function lrma_mc_widget_src( string $key ): string {
    return 'https://www.mixcloud.com/widget/iframe/?feed=' . rawurlencode( 'https://www.mixcloud.com' . $key ) . '&hide_cover=1&hide_artwork=0';
}
?>

<!-- ╔══════════════════════════════════════╗
     ║  JAUNĀKIE RAIDĪJUMI                 ║
     ╚══════════════════════════════════════╝ -->
<?php $placeholder_img = esc_url( get_template_directory_uri() . '/assets/img/placeholder.svg' ); ?>
<section id="radio" class="lrma-shows-section">
    <div class="lrma-shows-inner">

        <div class="lrma-shows-header">
            <div>
                <div class="section-label">Raidījumi</div>
                <h2 class="section-title">Jaunākie Raidījumi</h2>
            </div>
            <a href="https://www.mixcloud.com/LRMA/" target="_blank" rel="noopener" class="section-all-link">Visi Raidījumi &nbsp;↗</a>
        </div>

        <div class="lrma-shows-grid reveal">

            <!-- Platais Vakars -->
            <a href="<?php echo esc_url( $pv['url'] ); ?>" target="_blank" rel="noopener" class="lrma-show-card">
                <div class="lrma-show-thumb-wrap">
                    <img src="<?php echo esc_url( $pv['thumb'] ); ?>"
                         alt="<?php echo esc_attr( 'Platais Vakars — ' . $pv['name'] ); ?>"
                         onerror="this.onerror=null;this.src='<?php echo $placeholder_img; ?>'"
                         loading="lazy">
                    <div class="lrma-show-thumb-overlay">
                        <svg class="mc-play-icon" viewBox="0 0 24 24" fill="white"><polygon points="5,3 19,12 5,21"/></svg>
                    </div>
                </div>
                <div class="lrma-show-card-body">
                    <div class="lrma-show-name">Platais Vakars</div>
                    <div class="lrma-show-title"><?php echo esc_html( $pv['name'] ); ?></div>
                    <?php if ( $pv['date'] ) : ?>
                    <div class="lrma-show-date"><?php echo esc_html( $pv['date'] ); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mc-player">
                    <iframe width="100%" height="120"
                        src="<?php echo esc_url( lrma_mc_widget_src( $pv['key'] ) ); ?>"
                        frameborder="0"
                        allow="encrypted-media; fullscreen; autoplay; idle-detection; speaker-selection; web-share;"
                        loading="lazy"></iframe>
                </div>
            </a>

            <!-- Roka Nemieri -->
            <a href="<?php echo esc_url( $rn['url'] ); ?>" target="_blank" rel="noopener" class="lrma-show-card">
                <div class="lrma-show-thumb-wrap">
                    <img src="<?php echo esc_url( $rn['thumb'] ); ?>"
                         alt="<?php echo esc_attr( 'Roka Nemieri — ' . $rn['name'] ); ?>"
                         onerror="this.onerror=null;this.src='<?php echo $placeholder_img; ?>'"
                         loading="lazy">
                    <div class="lrma-show-thumb-overlay">
                        <svg class="mc-play-icon" viewBox="0 0 24 24" fill="white"><polygon points="5,3 19,12 5,21"/></svg>
                    </div>
                </div>
                <div class="lrma-show-card-body">
                    <div class="lrma-show-name">Roka Nemieri</div>
                    <div class="lrma-show-title"><?php echo esc_html( $rn['name'] ); ?></div>
                    <?php if ( $rn['date'] ) : ?>
                    <div class="lrma-show-date"><?php echo esc_html( $rn['date'] ); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mc-player">
                    <iframe width="100%" height="120"
                        src="<?php echo esc_url( lrma_mc_widget_src( $rn['key'] ) ); ?>"
                        frameborder="0"
                        allow="encrypted-media; fullscreen; autoplay; idle-detection; speaker-selection; web-share;"
                        loading="lazy"></iframe>
                </div>
            </a>

        </div><!-- .lrma-shows-grid -->

    </div><!-- .lrma-shows-inner -->
</section>

<!-- ╔══════════════════════════════════════╗
     ║  CONCERTS                           ║
     ╚══════════════════════════════════════╝ -->
<?php
$lv_months = [ 'Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'Mai','Jun'=>'Jūn','Jul'=>'Jūl','Aug'=>'Aug','Sep'=>'Sep','Oct'=>'Okt','Nov'=>'Nov','Dec'=>'Dec' ];

/* ── Priority 1: custom CPT posts ─────────────────────────────────── */
$cpt_concerts = new WP_Query( [
    'post_type'      => 'koncerti',
    'posts_per_page' => 5,
    'orderby'        => 'meta_value',
    'meta_key'       => 'concert_date',
    'order'          => 'ASC',
    'post_status'    => 'publish',
] );

/* ── Priority 2: live feed (LRM-63, functions.php) ───────────────── */
/* Returns: [ 'title', 'url', 'thumb', 'date' (Unix ts) ] */
$raw_live = $cpt_concerts->have_posts() ? [] : lrma_get_upcoming_concerts();
$live_concerts = [];
foreach ( $raw_live as $c ) {
    $month_en    = $c['date'] ? date( 'M', $c['date'] ) : '';
    $month_label = $lv_months[ $month_en ] ?? $month_en;
    // Append short year suffix if not current year (e.g. " '26")
    if ( ! empty( $c['date_year'] ) && $c['date_year'] !== (int) date( 'Y' ) ) {
        $month_label .= " '" . substr( (string) $c['date_year'], 2 );
    }
    $live_concerts[] = [
        'day'   => $month_label ?: '—',
        'month' => '',
        'name'  => $c['title'],
        'venue' => '',
        'url'   => $c['url'],
        'thumb' => ! empty( $c['thumb'] ) ? $c['thumb'] : null,
    ];
}

/* ── Priority 3: hardcoded fallback ──────────────────────────────── */
$fallback_concerts = [
    [ 'day' => '07', 'month' => 'Apr', 'name' => 'Thrown – Tour 2026',                    'venue' => 'Angars Concert Hall, Rīga', 'url' => 'https://en.concerts-metal.com/LV__Latvia', 'thumb' => null ],
    [ 'day' => '02', 'month' => 'Mai', 'name' => 'The 69 Eyes – I Survive Tour 2026',     'venue' => 'Melnā Piektdiena, Rīga',    'url' => 'https://en.concerts-metal.com/LV__Latvia', 'thumb' => null ],
    [ 'day' => '28', 'month' => 'Mai', 'name' => 'Laibach',                               'venue' => 'Spelet, Rīga',              'url' => 'https://en.concerts-metal.com/LV__Latvia', 'thumb' => null ],
    [ 'day' => '16', 'month' => 'Jūn', 'name' => 'Bury Tomorrow – Tour 2026',             'venue' => 'Melnā Piektdiena, Rīga',    'url' => 'https://en.concerts-metal.com/LV__Latvia', 'thumb' => null ],
    [ 'day' => '29', 'month' => 'Jūn', 'name' => 'Blood Incantation – Absolute Elsewhere', 'venue' => 'Spelet, Rīga',             'url' => 'https://en.concerts-metal.com/LV__Latvia', 'thumb' => null ],
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
            $month      = $lv_months[ $month_en ] ?? $month_en;
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
        foreach ( $display as $c ) :
            $thumb = $c['thumb'] ?? null;
        ?>
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
                <?php if ( $thumb ) : ?>
                <div class="concert-thumb"><img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $c['name'] ); ?>" loading="lazy"></div>
                <?php endif; ?>
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
        <div class="concerts-attribution">Dati: <a href="https://www.concerts-metal.com" target="_blank" rel="noopener">concerts-metal.com</a></div>
    </div>

</section>

<!-- ╔══════════════════════════════════════╗
     ║  STATS STRIP                        ║
     ╚══════════════════════════════════════╝ -->
<div class="stats-strip reveal">
    <div class="stat-block">
        <span class="stat-num"><?php echo date( 'Y' ) - 2016; ?><em>+</em></span>
        <span class="stat-lbl">Gadi Aktīvi</span>
    </div>
    <div class="stat-block">
        <span class="stat-num"><?php echo esc_html( get_theme_mod( 'lrma_footer_followers', '10K' ) ); ?><em>+</em></span>
        <span class="stat-lbl">Sekotāji</span>
    </div>
    <div class="stat-block">
        <span class="stat-num">24<em>/7</em></span>
        <span class="stat-lbl">Radio Tiešraide</span>
    </div>
    <div class="stat-block">
        <span class="stat-num"><?php echo number_format( wp_count_posts()->publish ); ?><em>+</em></span>
        <span class="stat-lbl">Publicēti Raksti</span>
    </div>
</div>

<?php get_footer(); ?>
