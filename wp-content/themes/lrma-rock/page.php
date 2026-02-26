<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<div class="page-hero">
    <h1 class="page-title"><?php the_title(); ?></h1>
    <?php if ( get_the_excerpt() ) : ?>
        <p class="archive-desc"><?php echo esc_html( get_the_excerpt() ); ?></p>
    <?php endif; ?>
</div>

<div class="page-content">
    <?php the_content(); ?>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
