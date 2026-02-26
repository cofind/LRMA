<?php get_header(); ?>

<div class="archive-header">
    <?php if ( is_home() && ! is_front_page() ) : ?>
        <div class="section-label">Visi Raksti</div>
        <h1 class="archive-title">Jaunumi</h1>
    <?php elseif ( is_category() ) : ?>
        <div class="section-label">Kategorija</div>
        <h1 class="archive-title"><?php single_cat_title(); ?></h1>
        <?php if ( category_description() ) : ?>
            <p class="archive-desc"><?php echo category_description(); ?></p>
        <?php endif; ?>
    <?php elseif ( is_tag() ) : ?>
        <div class="section-label">Tēmaturis</div>
        <h1 class="archive-title">#<?php single_tag_title(); ?></h1>
    <?php elseif ( is_author() ) : ?>
        <div class="section-label">Autors</div>
        <h1 class="archive-title"><?php the_author(); ?></h1>
    <?php elseif ( is_search() ) : ?>
        <div class="section-label">Meklēšana</div>
        <h1 class="archive-title">"<?php the_search_query(); ?>"</h1>
    <?php else : ?>
        <h1 class="archive-title">Raksti</h1>
    <?php endif; ?>
</div>

<?php if ( have_posts() ) : ?>
<div class="archive-grid">
    <?php while ( have_posts() ) : the_post(); ?>
    <a href="<?php the_permalink(); ?>" class="news-card">
        <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail( 'medium', [ 'class' => 'card-image' ] ); ?>
        <?php endif; ?>
        <div class="card-tag">
            <?php $cats = get_the_category(); if ( $cats ) echo esc_html( $cats[0]->name ); ?>
        </div>
        <h3 class="card-title"><?php the_title(); ?></h3>
        <p class="card-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 18, '…' ); ?></p>
        <div class="card-meta"><?php echo get_the_date( 'd.m.Y' ); ?></div>
    </a>
    <?php endwhile; ?>
</div>

<div class="archive-pagination">
    <?php the_posts_pagination( [
        'mid_size'  => 2,
        'prev_text' => '&larr;',
        'next_text' => '&rarr;',
    ] ); ?>
</div>

<?php else : ?>
<div style="padding:120px 40px;text-align:center;">
    <p style="font-family:var(--font-mono);font-size:13px;letter-spacing:0.1em;color:var(--muted);">Nav atrastu rakstu.</p>
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-outline" style="margin-top:24px;display:inline-flex;">Uz sākumu</a>
</div>
<?php endif; ?>

<?php get_footer(); ?>
