<?php get_header(); ?>

<?php global $wp_query; ?>

<div class="archive-header">
	<div>
		<div class="section-label">Meklēšana</div>
		<h1 class="archive-title">
			<?php if ( get_search_query() ) : ?>
				"<?php echo esc_html( get_search_query() ); ?>"
			<?php else : ?>
				Meklēt
			<?php endif; ?>
		</h1>
	</div>
	<?php if ( have_posts() ) : ?>
	<div class="archive-count">
		<?php
		$total = $wp_query->found_posts;
		echo esc_html( $total ) . ' ' . ( $total === 1 ? 'rezultāts' : 'rezultāti' );
		?>
	</div>
	<?php endif; ?>
</div>

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
	<div class="archive-empty__label">
		Nav rezultātu<?php if ( get_search_query() ) : ?> par "<?php echo esc_html( get_search_query() ); ?>"<?php endif; ?>
	</div>
	<p style="color:var(--muted);font-size:13px;margin-bottom:24px;">Mēģini: Skyforger, Intervija, Festivāls, Metāls</p>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-outline">← Uz sākumu</a>
</div>

<?php
$recent = new WP_Query( [ 'posts_per_page' => 4, 'post_status' => 'publish' ] );
if ( $recent->have_posts() ) :
?>
<div class="archive-suggestions">
	<div class="archive-suggestions__kicker section-label">Jaunākie Raksti</div>
	<div class="archive-grid">
		<?php while ( $recent->have_posts() ) : $recent->the_post(); ?>
			<?php get_template_part( 'template-parts/card-article', null, [ 'post' => $GLOBALS['post'] ] ); ?>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
</div>
<?php endif; ?>

<?php endif; ?>

<?php get_footer(); ?>
