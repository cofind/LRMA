<?php
/**
 * Single slide template
 *
 * $args['slide']  — associative array of slide data
 * $args['index']  — zero-based slide index
 */

$slide = $args['slide'] ?? [];
$index = (int) ( $args['index'] ?? 0 );

if ( empty( $slide['title'] ) && empty( $slide['image'] ) ) return;

$type   = esc_attr( $slide['type']   ?? 'article' );
$accent = esc_attr( $slide['accent'] ?? 'red' );
$image  = $slide['image']    ?? '';
$cat    = $slide['cat']      ?? '';
$title  = $slide['title']    ?? '';
$meta   = $slide['meta']     ?? '';
$cta    = $slide['cta_text'] ?? ( $slide['type'] === 'article' ? 'Lasīt Rakstu' : 'Uzzināt Vairāk' );
$url    = $slide['cta_url']  ?? '#';

$badge       = $slide['type'] === 'article' ? '● Raksts' : '◈ Promo';
$badge_class = $slide['type'] === 'article' ? 'badge-article' : 'badge-promo';
?>
<div class="slide type-<?php echo $type; ?> accent-<?php echo $accent; ?>"
     aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>">

	<?php if ( $image ) : ?>
	<div class="slide-bg" style="background-image:url('<?php echo esc_url( $image ); ?>');" role="img" aria-hidden="true"></div>
	<?php endif; ?>

	<div class="slide-overlay" aria-hidden="true"></div>

	<span class="slide-type-badge <?php echo $badge_class; ?>"><?php echo esc_html( $badge ); ?></span>

	<div class="slide-inner">
		<?php if ( $cat ) : ?>
		<div class="slide-cat"><?php echo esc_html( $cat ); ?></div>
		<?php endif; ?>

		<h2 class="slide-title"><?php echo esc_html( $title ); ?></h2>

		<?php if ( $meta ) : ?>
		<p class="slide-meta"><?php echo esc_html( $meta ); ?></p>
		<?php endif; ?>

		<a href="<?php echo esc_url( $url ); ?>" class="slide-cta"
		   <?php echo $slide['type'] === 'promo' && $url !== '#' ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
			<?php echo esc_html( $cta ); ?> →
		</a>
	</div>

</div>
