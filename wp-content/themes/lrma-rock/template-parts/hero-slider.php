<?php
/**
 * LRMA Hero Slider — Mixed Article + Promo slides
 *
 * Type A — Article slides  : auto-pulled from WP_Query (latest posts with thumbnail)
 * Type B — Promo slides    : manually curated via ACF Options (requires ACF Pro)
 */

// ── 1. Article slides ─────────────────────────────────────────────────────────
$article_slides = [];
$_sq = new WP_Query( [
	'posts_per_page' => 4,
	'post_status'    => 'publish',
	'meta_query'     => [ [ 'key' => '_thumbnail_id', 'compare' => 'EXISTS' ] ],
] );
while ( $_sq->have_posts() ) {
	$_sq->the_post();
	$article_slides[] = [
		'type'     => 'article',
		'position' => 99,
		'image'    => get_the_post_thumbnail_url( null, 'lrma-hero' ),
		'cat'      => get_the_category()[0]->name ?? '',
		'title'    => get_the_title(),
		'meta'     => get_the_date( 'd.m.Y' ) . ' · ' . lrma_read_time() . ' min',
		'cta_text' => 'Lasīt Rakstu',
		'cta_url'  => get_permalink(),
		'accent'   => 'red',
	];
}
wp_reset_postdata();

// ── 2. Promo slides from ACF Options (requires ACF Pro) ───────────────────────
$promo_slides = [];
if ( function_exists( 'get_field' ) ) {
	$rows = get_field( 'featured_slides', 'option' ) ?: [];
	foreach ( $rows as $row ) {
		if ( empty( $row['active'] ) ) continue;
		$promo_slides[] = [
			'type'     => 'promo',
			'position' => (int) ( $row['position'] ?? 99 ),
			'image'    => $row['image']['url'] ?? '',
			'cat'      => $row['cat_label'] ?? '',
			'title'    => $row['title'] ?? '',
			'meta'     => $row['subtitle'] ?? '',
			'cta_text' => $row['cta_text'] ?? 'Uzzināt Vairāk',
			'cta_url'  => $row['cta_url'] ?? '#',
			'accent'   => $row['accent_colour'] ?? 'red',
		];
	}
}

// ── 3. Merge, sort by position, cap at 6 ─────────────────────────────────────
$slides = array_merge( $article_slides, $promo_slides );
usort( $slides, fn( $a, $b ) => $a['position'] <=> $b['position'] );
$slides = array_slice( $slides, 0, 6 );

if ( empty( $slides ) ) return;

$count = count( $slides );
?>
<div class="lrma-slider" role="region" aria-label="Jaunākie raksti un aktualitātes" data-count="<?php echo $count; ?>">

	<?php foreach ( $slides as $i => $slide ) :
		get_template_part( 'template-parts/slide', null, [ 'slide' => $slide, 'index' => $i ] );
	endforeach; ?>

	<?php if ( $count > 1 ) : ?>

	<div class="slider-dots" role="tablist" aria-label="Slaideru navigācija">
		<?php foreach ( $slides as $i => $slide ) : ?>
		<button class="slider-dot dot-type-<?php echo esc_attr( $slide['type'] ); ?>"
		        role="tab"
		        aria-label="<?php echo esc_attr( sprintf( 'Slaids %d', $i + 1 ) ); ?>"
		        aria-selected="false"
		        data-index="<?php echo $i; ?>"></button>
		<?php endforeach; ?>
	</div>

	<button class="slider-arrow slider-prev" aria-label="Iepriekšējais slaids">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15,18 9,12 15,6"/></svg>
	</button>
	<button class="slider-arrow slider-next" aria-label="Nākamais slaids">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9,6 15,12 9,18"/></svg>
	</button>

	<?php endif; ?>

	<div class="slider-progress-bar" aria-hidden="true">
		<div class="slider-progress-fill"></div>
	</div>

</div><!-- .lrma-slider -->

<script>
(function () {
	'use strict';
	var slider = document.querySelector('.lrma-slider');
	if (!slider) return;

	var slides   = slider.querySelectorAll('.slide');
	var dots     = slider.querySelectorAll('.slider-dot');
	var fill     = slider.querySelector('.slider-progress-fill');
	var DURATION = 5500;
	var current  = 0;
	var paused   = false;
	var rafId    = null;
	var startAt  = null;

	function goTo(idx) {
		slides[current].classList.remove('is-active');
		slides[current].setAttribute('aria-hidden', 'true');
		if (dots.length) {
			dots[current].classList.remove('is-active');
			dots[current].setAttribute('aria-selected', 'false');
		}
		current = ((idx % slides.length) + slides.length) % slides.length;
		slides[current].classList.add('is-active');
		slides[current].setAttribute('aria-hidden', 'false');
		if (dots.length) {
			dots[current].classList.add('is-active');
			dots[current].setAttribute('aria-selected', 'true');
		}
		stopProgress();
		if (!paused) startProgress();
	}

	function stopProgress() {
		if (rafId) { cancelAnimationFrame(rafId); rafId = null; }
		startAt = null;
		if (fill) fill.style.width = '0%';
	}

	function startProgress() {
		startAt = performance.now();
		rafId = requestAnimationFrame(tick);
	}

	function tick(now) {
		if (paused || !startAt) return;
		var elapsed = now - startAt;
		var pct = Math.min(elapsed / DURATION * 100, 100);
		if (fill) fill.style.width = pct + '%';
		if (elapsed >= DURATION) {
			goTo(current + 1);
		} else {
			rafId = requestAnimationFrame(tick);
		}
	}

	// Initialise first slide
	slides[0].classList.add('is-active');
	slides[0].setAttribute('aria-hidden', 'false');
	if (dots.length) {
		dots[0].classList.add('is-active');
		dots[0].setAttribute('aria-selected', 'true');
	}
	startProgress();

	// Pause on hover
	slider.addEventListener('mouseenter', function () {
		paused = true;
		if (rafId) { cancelAnimationFrame(rafId); rafId = null; }
	});
	slider.addEventListener('mouseleave', function () {
		paused = false;
		startProgress();
	});

	// Touch swipe
	var touchX = 0;
	slider.addEventListener('touchstart', function (e) {
		touchX = e.touches[0].clientX;
		paused = true;
		if (rafId) { cancelAnimationFrame(rafId); rafId = null; }
	}, { passive: true });
	slider.addEventListener('touchend', function (e) {
		var diff = touchX - e.changedTouches[0].clientX;
		if (Math.abs(diff) > 40) goTo(current + (diff > 0 ? 1 : -1));
		paused = false;
		startProgress();
	});

	// Keyboard navigation (only when slider is focused/visible)
	document.addEventListener('keydown', function (e) {
		if (!slider.closest('body')) return;
		if (e.key === 'ArrowLeft')  goTo(current - 1);
		if (e.key === 'ArrowRight') goTo(current + 1);
	});

	// Dot clicks
	dots.forEach(function (dot) {
		dot.addEventListener('click', function () {
			goTo(parseInt(this.dataset.index, 10));
		});
	});

	// Arrow clicks
	var prevBtn = slider.querySelector('.slider-prev');
	var nextBtn = slider.querySelector('.slider-next');
	if (prevBtn) prevBtn.addEventListener('click', function () { goTo(current - 1); });
	if (nextBtn) nextBtn.addEventListener('click', function () { goTo(current + 1); });
}());
</script>
