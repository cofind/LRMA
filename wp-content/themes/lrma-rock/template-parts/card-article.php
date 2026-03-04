<?php
/**
 * Reusable article card
 *
 * $args['post']    — WP_Post object (required)
 * $args['variant'] — 'large' | 'medium' | 'small'  (default: 'medium')
 *
 * Used by: front-page.php (editorial grid + strip), LRM-64 (archive pages)
 */

$card_post = $args['post'] ?? null;
if ( ! $card_post ) return;

$variant   = $args['variant'] ?? 'medium';
$permalink = get_permalink( $card_post );
$title     = get_the_title( $card_post );
$cats      = get_the_category( $card_post->ID );

// Context-aware category label: prefer the category matching the current archive,
// or an explicit context arg (e.g. 'koncerti'), to avoid always showing 'Jaunumi'.
$cat_name = '';
if ( ! empty( $args['context'] ) ) {
	foreach ( $cats as $c ) {
		if ( $c->slug === $args['context'] ) {
			$cat_name = $c->name;
			break;
		}
	}
}
if ( ! $cat_name && is_category() ) {
	$queried = get_queried_object();
	foreach ( $cats as $c ) {
		if ( $c->term_id === $queried->term_id ) {
			$cat_name = $c->name;
			break;
		}
	}
}
if ( ! $cat_name ) {
	$cat_name = $cats ? $cats[0]->name : '';
}
$date      = get_the_date( 'd.m.Y', $card_post );
$read_min  = lrma_read_time( $card_post->ID );

$thumb_size = match ( $variant ) {
	'large' => 'lrma-hero',
	'small' => 'lrma-sq',
	default => 'lrma-card',
};
$thumb_url = get_the_post_thumbnail_url( $card_post, $thumb_size );
?>
<a href="<?php echo esc_url( $permalink ); ?>"
   class="article-card variant-<?php echo esc_attr( $variant ); ?>">

	<?php if ( $thumb_url && $variant === 'large' ) : ?>
	<div class="article-card__img article-card__img--cover"
	     style="background-image:url('<?php echo esc_url( $thumb_url ); ?>')"
	     role="img" aria-hidden="true"></div>
	<div class="article-card__overlay" aria-hidden="true"></div>

	<?php elseif ( $thumb_url ) : ?>
	<div class="article-card__img">
		<img src="<?php echo esc_url( $thumb_url ); ?>"
		     alt="<?php echo esc_attr( $title ); ?>"
		     loading="lazy">
	</div>
	<?php endif; ?>

	<div class="article-card__body">
		<?php if ( $cat_name ) : ?>
		<div class="card-tag"><?php echo esc_html( $cat_name ); ?></div>
		<?php endif; ?>

		<h3 class="article-card__title"><?php echo esc_html( $title ); ?></h3>

		<div class="card-meta">
			<?php echo esc_html( $date ); ?>
			<?php if ( $variant === 'large' ) : ?> · <?php echo esc_html( $read_min ); ?> min<?php endif; ?>
		</div>

		<?php if ( $variant === 'large' ) : ?>
		<span class="article-card__cta">Lasīt Rakstu →</span>
		<?php endif; ?>
	</div>

</a>
