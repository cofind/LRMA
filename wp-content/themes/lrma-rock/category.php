<?php get_header(); ?>

<?php if ( is_category( 'koncerti' ) ) : ?>

<?php
// Unified feed: recent Koncerti articles + upcoming concert events
$koncerti_feed = lrma_get_koncerti_feed( 12 );
?>

<div class="archive-header">
	<div>
		<div class="section-label">Kategorija</div>
		<h1 class="archive-title">Koncerti</h1>
	</div>
	<a href="https://www.concerts-metal.com" target="_blank" rel="noopener" class="koncerti-attribution">
		Dati: concerts-metal.com
	</a>
</div>

<?php if ( ! empty( $koncerti_feed ) ) : ?>
<div class="archive-grid">
	<?php foreach ( $koncerti_feed as $item ) : lrma_render_koncerti_card( $item ); endforeach; ?>
</div>
<?php else : ?>
<div class="archive-empty">
	<div class="archive-empty__label">Pašlaik nav aktīvu pasākumu</div>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-outline">← Uz sākumu</a>
</div>
<?php endif; ?>

<div class="koncerti-archive-link">
	<?php
	$koncerti_cat = get_category_by_slug( 'koncerti' );
	$all_url      = $koncerti_cat ? esc_url( get_category_link( $koncerti_cat ) . '?all=1' ) : esc_url( home_url( '/category/koncerti/' ) );
	?>
	<a href="<?php echo $all_url; ?>" class="btn-outline-mono">Visi koncerti raksti &nbsp;→</a>
</div>

<?php else : ?>

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

<?php endif; ?>

<?php get_footer(); ?>
