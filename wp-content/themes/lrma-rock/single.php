<?php get_header(); ?>

<div id="reading-progress"></div>

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
			<?php echo lrma_read_time( get_the_ID() ); ?> min lasīšana
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

	<!-- TAGS -->
	<?php $tags = get_the_tags(); if ( $tags ) : ?>
	<div class="lrma-tags">
		<?php foreach ( $tags as $tag ) : ?>
			<a href="<?php echo esc_url( get_tag_link( $tag ) ); ?>" class="lrma-tag">#<?php echo esc_html( $tag->name ); ?></a>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<!-- SHARE BUTTONS -->
	<?php
	$share_url   = urlencode( get_permalink() );
	$share_title = urlencode( get_the_title() );
	?>
	<div class="lrma-share">
		<span class="lrma-share-label">Dalīties</span>
		<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" target="_blank" rel="noopener" class="lrma-share-btn">FB</a>
		<a href="https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&amp;text=<?php echo $share_title; ?>" target="_blank" rel="noopener" class="lrma-share-btn">X</a>
		<button class="lrma-share-btn" onclick="navigator.clipboard.writeText(window.location.href).then(function(){var b=this;b.textContent='Nokopēts!';setTimeout(function(){b.textContent='Kopēt';},2000)}.bind(this))">Kopēt</button>
	</div>

	<!-- AUTHOR BIO -->
	<?php $author_bio = get_the_author_meta( 'description' ); if ( $author_bio ) : ?>
	<div class="lrma-author-block">
		<?php echo get_avatar( get_the_author_meta( 'ID' ), 48, '', '', [ 'class' => 'lrma-author-avatar' ] ); ?>
		<div>
			<div class="lrma-author-name"><?php the_author(); ?></div>
			<div class="lrma-author-bio"><?php echo esc_html( $author_bio ); ?></div>
		</div>
	</div>
	<?php endif; ?>

</article>

<!-- NEXT ARTICLE TEASER -->
<?php $next_post = get_next_post(); if ( $next_post ) : ?>
<div class="lrma-next-article">
	<div class="lrma-next-kicker">Nākamais raksts</div>
	<?php
	$next_thumb = get_the_post_thumbnail_url( $next_post, 'large' );
	$next_cats  = get_the_category( $next_post->ID );
	$next_style = $next_thumb ? ' style="background-image:url(\'' . esc_url( $next_thumb ) . '\')"' : '';
	?>
	<a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>" class="lrma-next-card"<?php echo $next_style; ?>>
		<div class="lrma-next-overlay"></div>
		<div class="lrma-next-body">
			<?php if ( $next_cats ) : ?>
			<div class="lrma-next-cat"><?php echo esc_html( $next_cats[0]->name ); ?></div>
			<?php endif; ?>
			<div class="lrma-next-title"><?php echo esc_html( get_the_title( $next_post ) ); ?></div>
		</div>
	</a>
</div>
<?php endif; ?>

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
			<?php get_template_part( 'template-parts/card-article', null, [ 'post' => $GLOBALS['post'] ] ); ?>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
</section>
<?php endif; ?>

<?php endwhile; ?>

<script>
(function () {
	var bar     = document.getElementById('reading-progress');
	var article = document.querySelector('.article-body');
	if (!bar || !article) return;
	window.addEventListener('scroll', function () {
		var rect    = article.getBoundingClientRect();
		var total   = article.offsetHeight - window.innerHeight;
		var scrolled = Math.max(0, -rect.top);
		bar.style.width = Math.min(100, (scrolled / total) * 100) + '%';
	}, { passive: true });
}());
</script>

<?php get_footer(); ?>
