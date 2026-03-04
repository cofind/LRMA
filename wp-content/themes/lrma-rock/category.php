<?php get_header(); ?>

<div class="archive-header">
	<div>
		<div class="section-label">Kategorija</div>
		<h1 class="archive-title"><?php single_cat_title(); ?></h1>
		<?php if ( category_description() ) : ?>
			<p class="archive-desc"><?php echo category_description(); ?></p>
		<?php endif; ?>
	</div>
	<?php if ( have_posts() ) : ?>
	<div class="archive-count">
		<?php
		global $wp_query;
		$total = $wp_query->found_posts;
		echo esc_html( $total ) . ' ' . ( $total === 1 ? 'raksts' : 'raksti' );
		?>
	</div>
	<?php endif; ?>
</div>

<?php if ( is_category( 'koncerti' ) ) : ?>
<section class="koncerti-live">
	<div class="koncerti-live-header">
		<div class="section-label">Tuvākie Koncerti</div>
		<h2 class="koncerti-live-title">Live Listings</h2>
		<a href="https://www.concerts-metal.com" target="_blank" rel="noopener" class="koncerti-attribution">
			concerts-metal.com
		</a>
	</div>
	<div class="koncerti-iframe-wrap">
		<iframe
			title="Tuvākie koncerti Latvijā"
			width="100%"
			height="900"
			frameBorder="0"
			loading="lazy"
			src="https://www.concerts-metal.com/ie-502_0d0d0d_cccccc_b_l5__latvia.html"
		></iframe>
	</div>
</section>
<?php endif; ?>

<?php if ( have_posts() ) : ?>
<div class="archive-grid">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php get_template_part( 'template-parts/card-article', null, [ 'post' => $GLOBALS['post'] ] ); ?>
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
<div class="archive-empty">
	<div class="archive-empty__label">Nav rakstu šajā kategorijā</div>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-outline">← Uz sākumu</a>
</div>
<?php endif; ?>

<?php get_footer(); ?>
