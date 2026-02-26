<?php get_header(); ?>

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
     ║  NEWS GRID                          ║
     ╚══════════════════════════════════════╝ -->
<section id="jaunumi" class="news-section">
    <div class="section-label">Jaunumi</div>
    <h2 class="section-title">Jaunākie Raksti</h2>

    <?php
    $featured   = new WP_Query( [ 'posts_per_page' => 1, 'post_status' => 'publish' ] );
    $secondary  = new WP_Query( [ 'posts_per_page' => 3, 'offset' => 1, 'post_status' => 'publish' ] );
    $more_posts = new WP_Query( [ 'posts_per_page' => 4, 'offset' => 4, 'post_status' => 'publish' ] );
    ?>

    <div class="news-grid reveal">

        <?php if ( $featured->have_posts() ) : while ( $featured->have_posts() ) : $featured->the_post(); ?>
        <a href="<?php the_permalink(); ?>" class="news-card featured">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'large', [ 'class' => 'card-image' ] ); ?>
            <?php endif; ?>
            <div class="card-tag">
                <?php $cats = get_the_category(); if ( $cats ) echo esc_html( $cats[0]->name ); ?>
            </div>
            <h3 class="card-title featured-title"><?php the_title(); ?></h3>
            <p class="card-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 28, '…' ); ?></p>
            <div class="card-meta">
                <?php echo get_the_date( 'd.m.Y' ); ?> · <?php echo max( 1, ceil( str_word_count( get_the_content() ) / 200 ) ); ?> min
            </div>
        </a>
        <?php endwhile; wp_reset_postdata(); endif; ?>

        <?php if ( $secondary->have_posts() ) : while ( $secondary->have_posts() ) : $secondary->the_post();
        $is_first = ( $secondary->current_post === 0 ); ?>
        <a href="<?php the_permalink(); ?>" class="news-card<?php echo $is_first ? ' wide' : ''; ?>">
            <?php if ( ! $is_first && has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'medium', [ 'class' => 'card-image card-image-small' ] ); ?>
            <?php endif; ?>
            <div class="card-tag">
                <?php $cats = get_the_category(); if ( $cats ) echo esc_html( $cats[0]->name ); ?>
            </div>
            <h3 class="card-title"><?php the_title(); ?></h3>
            <div class="card-meta"><?php echo get_the_date( 'd.m.Y' ); ?></div>
        </a>
        <?php endwhile; wp_reset_postdata(); endif; ?>

    </div><!-- .news-grid -->

    <div class="more-grid reveal">
        <?php if ( $more_posts->have_posts() ) : while ( $more_posts->have_posts() ) : $more_posts->the_post(); ?>
        <a href="<?php the_permalink(); ?>" class="news-card">
            <div class="card-tag">
                <?php $cats = get_the_category(); if ( $cats ) echo esc_html( $cats[0]->name ); ?>
            </div>
            <h3 class="card-title"><?php the_title(); ?></h3>
            <div class="card-meta"><?php echo get_the_date( 'd.m.Y' ); ?></div>
        </a>
        <?php endwhile; wp_reset_postdata(); endif; ?>
    </div><!-- .more-grid -->

    <div class="section-footer">
        <?php $posts_page = get_permalink( get_option( 'page_for_posts' ) ); ?>
        <a href="<?php echo $posts_page ? esc_url( $posts_page ) : esc_url( home_url( '/jaunumi/' ) ); ?>" class="btn btn-outline">Visi Raksti &nbsp;→</a>
    </div>

</section>

<!-- ╔══════════════════════════════════════╗
     ║  INTERVIEW BANNER                   ║
     ╚══════════════════════════════════════╝ -->
<?php
$interview = new WP_Query( [
    'posts_per_page' => 1,
    'category_name'  => 'intervijas',
    'post_status'    => 'publish',
] );
if ( $interview->have_posts() ) : while ( $interview->have_posts() ) : $interview->the_post();
?>
<div class="interview-banner reveal">
    <div class="interview-img">
        <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail( 'large', [ 'class' => 'interview-bg-img' ] ); ?>
        <?php endif; ?>
        <div class="interview-img-text"><?php echo strtoupper( get_the_title() ); ?></div>
    </div>
    <div class="interview-content">
        <div class="card-tag">Intervija</div>
        <h3><?php the_title(); ?></h3>
        <p><?php echo wp_trim_words( get_the_excerpt(), 32, '…' ); ?></p>
        <div class="card-meta">
            <?php echo get_the_date( 'd.m.Y' ); ?> · <?php echo max( 1, ceil( str_word_count( get_the_content() ) / 200 ) ); ?> min lasīšana
        </div>
        <a href="<?php the_permalink(); ?>" class="read-more-link">Lasīt Interviju →</a>
    </div>
</div>
<?php endwhile; wp_reset_postdata(); endif; ?>

<!-- ╔══════════════════════════════════════╗
     ║  RADIO                              ║
     ╚══════════════════════════════════════╝ -->
<section id="radio" class="radio-section">
    <div class="radio-inner">

        <div class="radio-text reveal">
            <div class="section-label">24/7 Tiešraide</div>
            <h2 class="section-title">LRMA<br>Rock Radio</h2>
            <p class="radio-desc">Latvijas pirmā un vienīgā radiostacija, kas spēlē <em>tikai</em> Latvijā radītu roku, metālu, alternatīvo un pankroku — visu diennakti, bez reklāmām.</p>
            <a href="<?php echo esc_url( get_theme_mod( 'radio_url', 'https://rockradio.lv' ) ); ?>" target="_blank" rel="noopener" class="btn btn-red" style="margin-bottom:32px;display:inline-flex;">Doties uz RockRadio.lv ↗</a>

            <div class="show-list">
                <div class="show-row">
                    <div class="show-dot live"></div>
                    <div class="show-info">
                        <div class="show-name">LRMA Rock Radio — Tiešraide</div>
                        <div class="show-time">Tagad spēlē · 24/7 automātiskā programma</div>
                    </div>
                </div>
                <div class="show-row">
                    <div class="show-dot"></div>
                    <div class="show-info">
                        <div class="show-name">Roka Nemieri</div>
                        <div class="show-time">Katru trešdienu 20:00 · Radio SWH Rock</div>
                    </div>
                </div>
                <div class="show-row">
                    <div class="show-dot"></div>
                    <div class="show-info">
                        <div class="show-name">Platais Vakars</div>
                        <div class="show-time">Piektdienās 21:00 · Latvijas roka aina</div>
                    </div>
                </div>
                <div class="show-row">
                    <div class="show-dot"></div>
                    <div class="show-info">
                        <div class="show-name">Metālkāsts LV</div>
                        <div class="show-time">Svētdienās 18:00 · Smagā metāla podkāsts</div>
                    </div>
                </div>
            </div>
        </div><!-- .radio-text -->

        <div class="player-card reveal">
            <div class="player-badge"><span class="live-dot"></span> Tiešraide</div>
            <div class="player-top">
                <div class="waveform">
                    <div class="wbar"></div><div class="wbar"></div><div class="wbar"></div>
                    <div class="wbar"></div><div class="wbar"></div><div class="wbar"></div>
                </div>
                <div class="track-info">
                    <div class="track-label">Tagad Spēlē</div>
                    <div class="track-name">LRMA Rock Radio</div>
                    <div class="track-sub">Latvijas Roks · 24/7</div>
                </div>
            </div>
            <div class="player-controls">
                <button class="play-btn" id="playBtn" onclick="lrmaTogglePlay(this)" aria-label="Play/Pause">
                    <svg viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21" fill="white"/></svg>
                </button>
                <div class="vol-track" id="volTrack">
                    <div class="vol-fill" id="volFill"></div>
                    <div class="vol-thumb" id="volThumb"></div>
                </div>
                <span class="vol-label">VOL</span>
            </div>
        </div><!-- .player-card -->

    </div><!-- .radio-inner -->
</section>

<!-- ╔══════════════════════════════════════╗
     ║  CONCERTS                           ║
     ╚══════════════════════════════════════╝ -->
<section id="koncerti" class="concerts-section">
    <div class="section-label">Gaidāmie</div>
    <h2 class="section-title">Koncerti &amp; Festivāli</h2>

    <div class="concert-list reveal">
    <?php
    $concerts = new WP_Query( [
        'post_type'      => 'koncerti',
        'posts_per_page' => 8,
        'orderby'        => 'meta_value',
        'meta_key'       => 'concert_date',
        'order'          => 'ASC',
        'post_status'    => 'publish',
    ] );

    if ( $concerts->have_posts() ) :
        while ( $concerts->have_posts() ) : $concerts->the_post();
            $date_raw   = get_post_meta( get_the_ID(), 'concert_date', true );
            $venue      = get_post_meta( get_the_ID(), 'concert_venue', true );
            $ticket_url = get_post_meta( get_the_ID(), 'concert_ticket_url', true );
            $day        = $date_raw ? date( 'd', strtotime( $date_raw ) ) : '—';
            $month      = $date_raw ? date( 'M', strtotime( $date_raw ) ) : '—';
    ?>
    <div class="concert-row">
        <div class="concert-date">
            <span class="concert-day"><?php echo esc_html( $day ); ?></span>
            <span class="concert-month"><?php echo esc_html( $month ); ?></span>
        </div>
        <div class="concert-info">
            <div class="concert-name"><?php the_title(); ?></div>
            <?php if ( $venue ) : ?>
                <div class="concert-venue"><?php echo esc_html( $venue ); ?></div>
            <?php endif; ?>
        </div>
        <div class="concert-ticket">
            <?php if ( $ticket_url ) : ?>
                <a href="<?php echo esc_url( $ticket_url ); ?>" target="_blank" rel="noopener" class="ticket-btn">Biļetes</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endwhile; wp_reset_postdata();

    else :
        $hardcoded = [
            [ '28', 'Feb', 'Grails',                          'Melnā Piektdiena, Rīga',                '#' ],
            [ '14', 'Mar', 'Dzelzs Vilks — 25 gadu jubileja', 'Palladium, Rīga',                       '#' ],
            [ '03', 'Apr', 'Skyforger',                       'Jelgavas Olimpiskais centrs, Jelgava',   '#' ],
            [ '30', 'Mai', 'Jack White',                      'Turaidas pils, Sigulda',                 '#' ],
            [ '24', 'Jūl', 'Saldus Saule 2026',               'Saldus pilsētas parks, Saldus',          '#' ],
        ];
        foreach ( $hardcoded as $c ) : ?>
    <div class="concert-row">
        <div class="concert-date">
            <span class="concert-day"><?php echo esc_html( $c[0] ); ?></span>
            <span class="concert-month"><?php echo esc_html( $c[1] ); ?></span>
        </div>
        <div class="concert-info">
            <div class="concert-name"><?php echo esc_html( $c[2] ); ?></div>
            <div class="concert-venue"><?php echo esc_html( $c[3] ); ?></div>
        </div>
        <div class="concert-ticket">
            <a href="<?php echo esc_url( $c[4] ); ?>" class="ticket-btn">Biļetes</a>
        </div>
    </div>
        <?php endforeach;
    endif; ?>
    </div><!-- .concert-list -->

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
