<?php get_header(); ?>

<div class="archive-header">
	<div>
		<?php if ( is_category() ) : ?>
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
			<p class="archive-desc">Visi raksti no šī autora</p>
		<?php elseif ( is_year() ) : ?>
			<div class="section-label">Gads</div>
			<h1 class="archive-title"><?php the_date( 'Y' ); ?>.</h1>
		<?php elseif ( is_month() ) : ?>
			<div class="section-label">Mēnesis</div>
			<h1 class="archive-title"><?php the_date( 'F Y' ); ?></h1>
		<?php else : ?>
			<h1 class="archive-title">Arhīvs</h1>
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

<?php if ( have_posts() ) : ?>
<div class="archive-grid">
	<?php
	$card_count = 0;
	while ( have_posts() ) : the_post();
		$variant = ( $card_count === 0 && has_post_thumbnail() ) ? 'large' : 'medium';
		$card_count++;
	?>
		<?php get_template_part( 'template-parts/card-article', null, [ 'post' => $GLOBALS['post'], 'variant' => $variant ] ); ?>
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
