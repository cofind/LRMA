<?php
/**
 * Template Name: Jaunumi
 * Template Post Type: page
 */
get_header();

$paged    = get_query_var( 'page' ) ? absint( get_query_var( 'page' ) ) : 1;
$per_page = 10;

$news = new WP_Query( [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => $per_page,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
] );
?>

<div class="jn-wrap">

    <header class="jn-header">
        <div class="section-label">Visi Raksti</div>
        <h1 class="jn-heading">Jaunumi</h1>
    </header>

    <?php if ( $news->have_posts() ) : ?>

        <?php if ( $paged === 1 ) : ?>
        <!-- ── FEATURED (most recent) ── -->
        <?php
        $news->the_post();
        $cats    = get_the_category();
        $cat     = $cats ? esc_html( $cats[0]->name ) : '';
        $readmin = max( 1, ceil( str_word_count( get_the_content() ) / 200 ) );
        ?>
        <a href="<?php the_permalink(); ?>" class="jn-featured">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'full', [ 'class' => 'jn-featured-img' ] ); ?>
            <?php endif; ?>
            <div class="jn-featured-overlay"></div>
            <div class="jn-featured-body">
                <div class="jn-featured-eyebrow">
                    <?php if ( $cat ) : ?><span class="card-tag"><?php echo $cat; ?></span><?php endif; ?>
                    <span class="jn-featured-label">Jaunākais</span>
                </div>
                <h2 class="jn-featured-title"><?php the_title(); ?></h2>
                <p class="jn-featured-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 26, '…' ); ?></p>
                <div class="jn-featured-meta">
                    <?php echo get_the_date( 'd.m.Y' ); ?> &nbsp;&middot;&nbsp; <?php echo $readmin; ?> min lasīšana
                </div>
            </div>
        </a>
        <?php endif; ?>

        <!-- ── ARTICLE LIST ── -->
        <?php if ( $news->have_posts() ) : ?>
        <ul class="jn-list">
            <?php $i = 0; while ( $news->have_posts() ) : $news->the_post();
                $cats    = get_the_category();
                $cat     = $cats ? esc_html( $cats[0]->name ) : '';
                $readmin = max( 1, ceil( str_word_count( get_the_content() ) / 200 ) );
                $i++;
            ?>
            <li>
                <a href="<?php the_permalink(); ?>" class="jn-item">
                    <span class="jn-item-num"><?php echo str_pad( ( $paged === 1 ? $i + 1 : ( ($paged-1)*$per_page + $i ) ), 2, '0', STR_PAD_LEFT ); ?></span>

                    <?php if ( has_post_thumbnail() ) : ?>
                    <span class="jn-item-thumb">
                        <?php the_post_thumbnail( 'medium', [ 'class' => 'jn-thumb-img' ] ); ?>
                    </span>
                    <?php endif; ?>

                    <span class="jn-item-content">
                        <span class="jn-item-top">
                            <?php if ( $cat ) : ?><span class="card-tag"><?php echo $cat; ?></span><?php endif; ?>
                            <span class="card-meta"><?php echo get_the_date( 'd.m.Y' ); ?> &nbsp;&middot;&nbsp; <?php echo $readmin; ?> min</span>
                        </span>
                        <span class="jn-item-title"><?php the_title(); ?></span>
                        <span class="jn-item-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 18, '…' ); ?></span>
                    </span>

                    <span class="jn-item-arrow">→</span>
                </a>
            </li>
            <?php endwhile; wp_reset_postdata(); ?>
        </ul>
        <?php endif; ?>

        <!-- ── PAGINATION ── -->
        <?php if ( $news->max_num_pages > 1 ) : ?>
        <nav class="jn-pagination" aria-label="Lapas">
            <?php echo paginate_links( [
                'base'      => trailingslashit( get_permalink() ) . '%_%',
                'format'    => 'page/%#%/',
                'current'   => $paged,
                'total'     => $news->max_num_pages,
                'prev_text' => '&larr;',
                'next_text' => '&rarr;',
                'mid_size'  => 2,
            ] ); ?>
        </nav>
        <?php endif; ?>

    <?php else : ?>
        <div class="jn-empty">
            <p>Nav rakstu.</p>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-outline">Uz sākumu</a>
        </div>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
