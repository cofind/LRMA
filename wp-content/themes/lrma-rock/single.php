<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<article class="single-article">

    <!-- ARTICLE HERO -->
    <div class="article-hero">
        <div class="article-cat">
            <?php
            $cats = get_the_category();
            if ( $cats ) {
                echo '<a href="' . esc_url( get_category_link( $cats[0]->term_id ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
            }
            ?>
        </div>
        <h1 class="article-title"><?php the_title(); ?></h1>
        <div class="article-byline">
            <?php echo get_the_date( 'd.m.Y' ); ?>
            &nbsp;·&nbsp;
            <?php the_author(); ?>
            &nbsp;·&nbsp;
            <?php echo max( 1, ceil( str_word_count( get_the_content() ) / 200 ) ); ?> min lasīšana
        </div>
    </div>

    <!-- FEATURED IMAGE -->
    <?php if ( has_post_thumbnail() ) : ?>
    <div class="article-featured-img">
        <?php the_post_thumbnail( 'full', [ 'class' => 'article-hero-img' ] ); ?>
    </div>
    <?php endif; ?>

    <!-- ARTICLE BODY -->
    <div class="article-body">
        <?php the_content(); ?>
    </div>

    <!-- PREV / NEXT NAV -->
    <div class="article-nav">
        <div class="article-nav-prev">
            <?php $prev = get_previous_post(); if ( $prev ) : ?>
            <span class="article-nav-label">← Iepriekšējais</span>
            <a href="<?php echo esc_url( get_permalink( $prev ) ); ?>" class="article-nav-title"><?php echo esc_html( get_the_title( $prev ) ); ?></a>
            <?php endif; ?>
        </div>
        <div class="article-nav-next">
            <?php $next = get_next_post(); if ( $next ) : ?>
            <span class="article-nav-label">Nākamais →</span>
            <a href="<?php echo esc_url( get_permalink( $next ) ); ?>" class="article-nav-title"><?php echo esc_html( get_the_title( $next ) ); ?></a>
            <?php endif; ?>
        </div>
    </div>

</article>

<!-- RELATED POSTS -->
<?php
$current_cats = wp_get_post_categories( get_the_ID() );
$related = new WP_Query( [
    'posts_per_page' => 3,
    'category__in'   => $current_cats,
    'post__not_in'   => [ get_the_ID() ],
    'orderby'        => 'rand',
    'post_status'    => 'publish',
] );
if ( $related->have_posts() ) :
?>
<section class="related-section">
    <div class="section-label">Saistītie</div>
    <h2 class="section-title" style="font-size:clamp(28px,4vw,48px);margin-bottom:28px;">Saistītie Raksti</h2>
    <div class="related-grid">
        <?php while ( $related->have_posts() ) : $related->the_post(); ?>
        <a href="<?php the_permalink(); ?>" class="news-card">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'medium', [ 'class' => 'card-image' ] ); ?>
            <?php endif; ?>
            <div class="card-tag">
                <?php $cats = get_the_category(); if ( $cats ) echo esc_html( $cats[0]->name ); ?>
            </div>
            <h3 class="card-title"><?php the_title(); ?></h3>
            <div class="card-meta"><?php echo get_the_date( 'd.m.Y' ); ?></div>
        </a>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</section>
<?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>
