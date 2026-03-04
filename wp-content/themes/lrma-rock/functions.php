<?php
/**
 * LRMA Rock — Theme Functions
 */

// Admin tools
if ( is_admin() ) {
    require_once get_template_directory() . '/lrma-tagger.php';
}

// ─── Theme Setup ──────────────────────────────────────────────────────────────
function lrma_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', [
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ] );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' ] );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'wp-block-styles' );

    add_image_size( 'lrma-hero',  1920, 900,  true );
    add_image_size( 'lrma-card',  800,  450,  true );
    add_image_size( 'lrma-thumb', 400,  225,  true );
    add_image_size( 'lrma-sq',    300,  300,  true );

    register_nav_menus( [
        'primary' => 'Galvenā Navigācija',
        'footer'  => 'Kājenes Navigācija',
    ] );

    load_theme_textdomain( 'lrma', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'lrma_setup' );

// ─── Enqueue ──────────────────────────────────────────────────────────────────
function lrma_enqueue() {
    wp_enqueue_style(
        'lrma-fonts',
        'https://fonts.googleapis.com/css2?family=Anton&family=Barlow+Condensed:wght@900&family=Bebas+Neue&family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap',
        [], null
    );
    wp_enqueue_style( 'lrma-style', get_stylesheet_uri(), [ 'lrma-fonts' ], '2.9.0' );

    wp_enqueue_script( 'lrma-main', get_template_directory_uri() . '/assets/js/main.js', [], '2.2.0', true );
    wp_enqueue_script( 'lrma-ajax-nav', get_template_directory_uri() . '/assets/js/ajax-nav.js', [], '1.0.0', true );
    wp_localize_script( 'lrma-main', 'lrmaData', [
        'radioStream' => get_theme_mod( 'radio_url', 'https://rockradio.lv' ),
        'homeUrl'     => home_url( '/' ),
    ] );
    wp_localize_script( 'lrma-main', 'lrmaAjax', [
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'lrma_newsletter_nonce' ),
    ] );
}
add_action( 'wp_enqueue_scripts', 'lrma_enqueue' );

// ─── Custom Post Type: Koncerti ───────────────────────────────────────────────
function lrma_register_cpt() {
    register_post_type( 'koncerti', [
        'labels' => [
            'name'          => 'Koncerti',
            'singular_name' => 'Koncerts',
            'add_new_item'  => 'Pievienot Koncertu',
        ],
        'public'        => true,
        'has_archive'   => true,
        'supports'      => [ 'title', 'editor', 'thumbnail' ],
        'menu_icon'     => 'dashicons-tickets-alt',
        'show_in_rest'  => true,
    ] );
}
add_action( 'init', 'lrma_register_cpt' );

// ─── Concert Meta Fields ──────────────────────────────────────────────────────
function lrma_register_concert_meta() {
    foreach ( [ 'concert_date', 'concert_venue', 'concert_ticket_url' ] as $field ) {
        register_post_meta( 'koncerti', $field, [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ] );
    }
}
add_action( 'init', 'lrma_register_concert_meta' );

// ─── Customizer ───────────────────────────────────────────────────────────────
function lrma_customizer( $wp_customize ) {
    $wp_customize->add_section( 'lrma_options', [
        'title'    => 'LRMA Settings',
        'priority' => 30,
    ] );

    // Hero visibility toggle
    $wp_customize->add_setting( 'show_hero', [
        'default'           => '1',
        'sanitize_callback' => 'absint',
    ] );
    $wp_customize->add_control( 'show_hero', [
        'label'   => 'Show Hero Section on front page',
        'section' => 'lrma_options',
        'type'    => 'checkbox',
    ] );

    $fields = [
        'radio_url'        => [ 'Radio Website URL',          'url',  'https://rockradio.lv' ],
        'radio_stream_url' => [ 'Radio Stream URL (mp3/aac)', 'url',  'https://c22.radioboss.fm/stream/318' ],
        'radio_meta_url'   => [ 'Radio Metadata JSON URL',    'url',  'https://c22.radioboss.fm/w/nowplayinginfo?u=318' ],
        'social_facebook'  => [ 'Facebook URL',            'url',  '' ],
        'social_instagram' => [ 'Instagram URL',           'url',  '' ],
        'social_youtube'   => [ 'YouTube URL',             'url',  '' ],
        'social_twitter'   => [ 'Twitter/X URL',           'url',  '' ],
        'site_email'       => [ 'Contact Email',           'text', 'info@lrma.lv' ],
    ];

    foreach ( $fields as $key => [ $label, $type, $default ] ) {
        $wp_customize->add_setting( $key, [
            'default'           => $default,
            'sanitize_callback' => $type === 'url' ? 'esc_url_raw' : 'sanitize_text_field',
        ] );
        $wp_customize->add_control( $key, [
            'label'   => $label,
            'section' => 'lrma_options',
            'type'    => $type,
        ] );
    }

    // Mixcloud — Platais Vakars
    $wp_customize->add_setting( 'lrma_platais_vakars_url', [ 'default' => 'https://www.mixcloud.com/LRMA/platais-vakars-p%C4%81vels-rutkovskis-2-da%C4%BCa-12012026/', 'sanitize_callback' => 'esc_url_raw' ] );
    $wp_customize->add_control( 'lrma_platais_vakars_url', [ 'label' => 'Platais Vakars — jaunākais epizodes URL (Mixcloud)', 'section' => 'lrma_options', 'type' => 'url' ] );
    $wp_customize->add_setting( 'lrma_platais_vakars_thumb', [ 'default' => 'https://thumbnailer.mixcloud.com/unsafe/600x600/extaudio/e/5/e/8/27f5-f168-4b00-882d-9d4f19ccfadb', 'sanitize_callback' => 'esc_url_raw' ] );
    $wp_customize->add_control( 'lrma_platais_vakars_thumb', [ 'label' => 'Platais Vakars — thumbnail URL', 'section' => 'lrma_options', 'type' => 'url' ] );
    $wp_customize->add_setting( 'lrma_platais_vakars_title', [ 'default' => 'PLATAIS VAKARS Pāvels Rutkovskis 2. daļa', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'lrma_platais_vakars_title', [ 'label' => 'Platais Vakars — epizodes nosaukums', 'section' => 'lrma_options', 'type' => 'text' ] );

    // Mixcloud — Roka Nemieri
    $wp_customize->add_setting( 'lrma_roka_nemieri_url', [ 'default' => 'https://www.mixcloud.com/radioswhrock/roka-nemieri-25022026/', 'sanitize_callback' => 'esc_url_raw' ] );
    $wp_customize->add_control( 'lrma_roka_nemieri_url', [ 'label' => 'Roka Nemieri — jaunākais epizodes URL (Mixcloud)', 'section' => 'lrma_options', 'type' => 'url' ] );
    $wp_customize->add_setting( 'lrma_roka_nemieri_thumb', [ 'default' => 'https://thumbnailer.mixcloud.com/unsafe/600x600/extaudio/7/3/b/c/f2b2-affb-45c7-9fa6-09035c0ea5d6', 'sanitize_callback' => 'esc_url_raw' ] );
    $wp_customize->add_control( 'lrma_roka_nemieri_thumb', [ 'label' => 'Roka Nemieri — thumbnail URL', 'section' => 'lrma_options', 'type' => 'url' ] );
    $wp_customize->add_setting( 'lrma_roka_nemieri_title', [ 'default' => 'Roka Nemieri (25.02.2026)', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'lrma_roka_nemieri_title', [ 'label' => 'Roka Nemieri — epizodes nosaukums', 'section' => 'lrma_options', 'type' => 'text' ] );

    // Followers count (editable by editors)
    $wp_customize->add_setting( 'lrma_footer_followers', [
        'default'           => '10K',
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'lrma_footer_followers', [
        'label'   => 'Sekotāju skaits (piemēram: 10K, 15K)',
        'section' => 'lrma_options',
        'type'    => 'text',
    ] );

    // Default OG image (1200×630 recommended)
    $wp_customize->add_setting( 'og_default_image', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( 'og_default_image', [
        'label'       => 'Default OG / Social Share Image URL (1200×630)',
        'description' => 'Used when a post has no featured image. Paste a full URL.',
        'section'     => 'lrma_options',
        'type'        => 'url',
    ] );

    // Logo height
    $wp_customize->add_setting( 'logo_height', [
        'default'           => 36,
        'sanitize_callback' => 'absint',
    ] );
    $wp_customize->add_control( 'logo_height', [
        'label'       => 'Logo Height (px)',
        'section'     => 'lrma_options',
        'type'        => 'number',
        'input_attrs' => [ 'min' => 20, 'max' => 120, 'step' => 1 ],
    ] );
}
add_action( 'customize_register', 'lrma_customizer' );

function lrma_logo_size_css() {
    $h   = max( 20, min( 120, absint( get_theme_mod( 'logo_height', 36 ) ) ) );
    $css = ".lrma-logo-img { height: {$h}px; }";
    wp_add_inline_style( 'lrma-style', $css );
}
add_action( 'wp_enqueue_scripts', 'lrma_logo_size_css', 20 );

// ─── Admin: Theme Settings page ───────────────────────────────────────────────
function lrma_admin_menu() {
    add_theme_page(
        'LRMA Theme Settings',
        'Theme Settings',
        'manage_options',
        'lrma-theme-settings',
        'lrma_theme_settings_page'
    );
}
add_action( 'admin_menu', 'lrma_admin_menu' );

function lrma_theme_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $saved = false;
    if ( isset( $_POST['lrma_save'] ) && check_admin_referer( 'lrma_theme_settings' ) ) {
        set_theme_mod( 'logo_height', max( 20, min( 120, absint( $_POST['logo_height'] ?? 36 ) ) ) );
        set_theme_mod( 'show_hero', isset( $_POST['show_hero'] ) ? 1 : 0 );
        $saved = true;
    }

    $logo_height = absint( get_theme_mod( 'logo_height', 36 ) );
    $show_hero   = (bool) get_theme_mod( 'show_hero', '1' );
    ?>
    <div class="wrap">
        <h1>LRMA Theme Settings</h1>

        <?php if ( $saved ) : ?>
            <div class="notice notice-success is-dismissible"><p><strong>Settings saved.</strong></p></div>
        <?php endif; ?>

        <form method="post">
            <?php wp_nonce_field( 'lrma_theme_settings' ); ?>

            <h2>Front Page</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">Hero Section</th>
                    <td>
                        <label>
                            <input type="checkbox"
                                   name="show_hero"
                                   value="1"
                                   <?php checked( $show_hero ); ?>>
                            Show the full-screen hero section at the top of the front page
                        </label>
                    </td>
                </tr>
            </table>

            <h2>Logo</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="logo_height">Logo Height (px)</label>
                    </th>
                    <td>
                        <input type="number"
                               id="logo_height"
                               name="logo_height"
                               value="<?php echo esc_attr( $logo_height ); ?>"
                               min="20" max="120" step="1"
                               class="small-text">
                        <p class="description">
                            Height of the header logo in pixels (20 – 120).
                            The logo image is set via <a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=title_tagline' ) ); ?>">Appearance → Customize → Site Identity</a>.
                        </p>
                    </td>
                </tr>
            </table>

            <?php submit_button( 'Save Settings', 'primary', 'lrma_save' ); ?>
        </form>
    </div>
    <?php
}

// ─── ACF Options Page (requires ACF Pro) ──────────────────────────────────────
function lrma_register_acf_options() {
    if ( ! function_exists( 'acf_add_options_page' ) ) return;

    acf_add_options_page( [
        'page_title' => 'LRMA Options',
        'menu_title' => 'LRMA Options',
        'menu_slug'  => 'lrma-options',
        'capability' => 'manage_options',
        'icon_url'   => 'dashicons-admin-site-alt3',
    ] );

    acf_add_options_sub_page( [
        'page_title'  => 'Featured Slides',
        'menu_title'  => 'Featured Slides',
        'parent_slug' => 'lrma-options',
    ] );
}
add_action( 'acf/init', 'lrma_register_acf_options' );

// ─── Helpers ──────────────────────────────────────────────────────────────────
add_filter( 'excerpt_length', fn() => 28 );
add_filter( 'excerpt_more',   fn() => '…' );

/**
 * Estimated reading time in minutes for the current post (or a given post ID).
 */
function lrma_read_time( $post_id = null ) {
    $content = $post_id
        ? get_post_field( 'post_content', $post_id )
        : get_the_content();
    return max( 1, (int) ceil( str_word_count( wp_strip_all_tags( $content ) ) / 200 ) );
}

function lrma_get_cat_name() {
    $cats = get_the_category();
    return $cats ? esc_html( $cats[0]->name ) : 'Jaunumi';
}

function lrma_get_cat_link() {
    $cats = get_the_category();
    return $cats ? esc_url( get_category_link( $cats[0]->term_id ) ) : home_url( '/' );
}

function lrma_thumb( $size = 'lrma-card', $fallback = true ) {
    if ( has_post_thumbnail() ) {
        return get_the_post_thumbnail_url( null, $size );
    }
    $external = get_post_meta( get_the_ID(), 'lrma_hero_image', true );
    if ( $external ) {
        return esc_url( $external );
    }
    if ( $fallback ) {
        return get_template_directory_uri() . '/assets/img/placeholder.svg';
    }
    return '';
}

// ─── Open Graph, Twitter Cards & JSON-LD ─────────────────────────────────────
function lrma_social_meta() {
    if ( is_admin() || is_feed() ) {
        return;
    }

    $site_name   = get_bloginfo( 'name' );
    $tagline     = get_bloginfo( 'description' );
    $home_url    = home_url( '/' );
    $default_img = get_theme_mod( 'og_default_image', '' );

    // Locale — respect Polylang if installed; only use full locale (e.g. lv_LV, en_US)
    $locale = 'lv_LV';
    if ( function_exists( 'pll_current_language' ) ) {
        $lang = pll_current_language( 'locale' );
        if ( $lang && str_contains( $lang, '_' ) ) {
            $locale = $lang;
        }
    }

    // Twitter site handle parsed from Customizer URL (e.g. https://twitter.com/lrma_lv → @lrma_lv)
    $twitter_handle = '';
    $twitter_url    = get_theme_mod( 'social_twitter', '' );
    if ( $twitter_url ) {
        $path = trim( wp_parse_url( $twitter_url, PHP_URL_PATH ), '/' );
        if ( $path ) {
            $twitter_handle = '@' . $path;
        }
    }

    // Per-context values
    $type     = 'website';
    $img_url  = $default_img;
    $img_w    = 1200;
    $img_h    = 630;
    $pub_time = '';
    $mod_time = '';
    $section  = '';
    $post     = null;
    $author_name = '';
    $author_url  = '';

    if ( is_singular() ) {
        $post    = get_queried_object();
        $title   = wp_strip_all_tags( $post->post_title );
        $excerpt = $post->post_excerpt
            ?: wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '…' );
        $desc    = $excerpt;
        $url     = get_permalink( $post );
        $type    = 'article';
        $pub_time = get_the_date( 'c', $post );
        $mod_time = get_the_modified_date( 'c', $post );

        $cats = get_the_category( $post->ID );
        if ( $cats ) {
            $section = $cats[0]->name;
        }

        if ( has_post_thumbnail( $post->ID ) ) {
            $src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'lrma-hero' );
            if ( $src ) {
                [ $img_url, $img_w, $img_h ] = $src;
            }
        }
        if ( ! $img_url ) {
            $external = get_post_meta( $post->ID, 'lrma_hero_image', true );
            if ( $external ) {
                $img_url = $external;
            }
        }
        if ( ! $img_url ) {
            $img_url = $default_img;
        }

        $author_name = get_the_author_meta( 'display_name', $post->post_author );
        $author_url  = get_author_posts_url( $post->post_author );

    } elseif ( is_category() || is_tag() || is_tax() ) {
        $term  = get_queried_object();
        $title = $term->name . ' — ' . $site_name;
        $desc  = $term->description ?: $tagline;
        $url   = get_term_link( $term );

    } elseif ( is_home() || is_front_page() ) {
        $title = $site_name;
        $desc  = $tagline;
        $url   = $home_url;

    } else {
        $title = wp_get_document_title();
        $desc  = $tagline;
        $url   = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
    }

    $title_esc = esc_attr( $title );
    $desc_esc  = esc_attr( wp_strip_all_tags( $desc ) );
    $url_esc   = esc_url( $url );

    ?>
<!-- Open Graph -->
<meta property="og:site_name"   content="<?php echo esc_attr( $site_name ); ?>">
<meta property="og:locale"      content="<?php echo esc_attr( $locale ); ?>">
<meta property="og:type"        content="<?php echo esc_attr( $type ); ?>">
<meta property="og:title"       content="<?php echo $title_esc; ?>">
<meta property="og:description" content="<?php echo $desc_esc; ?>">
<meta property="og:url"         content="<?php echo $url_esc; ?>">
<?php if ( $img_url ) : ?>
<meta property="og:image"        content="<?php echo esc_url( $img_url ); ?>">
<meta property="og:image:width"  content="<?php echo esc_attr( $img_w ); ?>">
<meta property="og:image:height" content="<?php echo esc_attr( $img_h ); ?>">
<meta property="og:image:alt"    content="<?php echo $title_esc; ?>">
<?php endif; ?>
<?php if ( $type === 'article' ) : ?>
<meta property="article:published_time" content="<?php echo esc_attr( $pub_time ); ?>">
<meta property="article:modified_time"  content="<?php echo esc_attr( $mod_time ); ?>">
<?php if ( $section ) : ?>
<meta property="article:section" content="<?php echo esc_attr( $section ); ?>">
<?php endif; ?>
<?php endif; ?>
<!-- Twitter Card -->
<meta name="twitter:card"        content="summary_large_image">
<?php if ( $twitter_handle ) : ?>
<meta name="twitter:site"        content="<?php echo esc_attr( $twitter_handle ); ?>">
<?php endif; ?>
<meta name="twitter:title"       content="<?php echo $title_esc; ?>">
<meta name="twitter:description" content="<?php echo $desc_esc; ?>">
<?php if ( $img_url ) : ?>
<meta name="twitter:image"       content="<?php echo esc_url( $img_url ); ?>">
<meta name="twitter:image:alt"   content="<?php echo $title_esc; ?>">
<?php endif; ?>
<?php
    // JSON-LD NewsArticle for single posts
    if ( $post && is_singular( 'post' ) ) {
        $logo_url = '';
        $logo_id  = get_theme_mod( 'custom_logo' );
        if ( $logo_id ) {
            $logo_src = wp_get_attachment_image_src( $logo_id, 'full' );
            if ( $logo_src ) {
                $logo_url = $logo_src[0];
            }
        }

        $schema = [
            '@context'      => 'https://schema.org',
            '@type'         => 'NewsArticle',
            'headline'      => wp_strip_all_tags( $post->post_title ),
            'description'   => wp_strip_all_tags( $desc ),
            'url'           => get_permalink( $post ),
            'datePublished' => $pub_time,
            'dateModified'  => $mod_time,
            'author'        => [
                '@type' => 'Person',
                'name'  => $author_name,
                'url'   => $author_url,
            ],
            'publisher' => array_filter( [
                '@type' => 'Organization',
                'name'  => $site_name,
                'url'   => $home_url,
                'logo'  => $logo_url ? [
                    '@type' => 'ImageObject',
                    'url'   => $logo_url,
                ] : null,
            ] ),
        ];

        if ( $img_url ) {
            $schema['image'] = [
                '@type'  => 'ImageObject',
                'url'    => $img_url,
                'width'  => $img_w,
                'height' => $img_h,
            ];
        }

        echo '<script type="application/ld+json">'
            . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT )
            . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'lrma_social_meta', 5 );

// ─── /koncerti/ → /category/koncerti/ redirect ────────────────────────────────
add_action( 'template_redirect', function () {
	if ( is_404() && rtrim( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' ) === '/koncerti' ) {
		wp_redirect( home_url( '/category/koncerti/' ), 301 );
		exit;
	}
} );

// ─── Koncerti: server-side fetch + unified feed ───────────────────────────────

/**
 * Fetch upcoming concerts from concerts-metal.com (Latvia, next 5).
 * Parses event dates from Unix timestamps embedded in flyer image filenames.
 * Cached 6h; returns [] on failure.
 */
function lrma_get_upcoming_concerts(): array {
	$cached = get_transient( 'lrma_concerts_feed' );
	if ( $cached !== false ) {
		return $cached;
	}

	$response = wp_remote_get(
		'https://broadcast.concerts-metal.com/ie-502_666666_000000_b_l5__latvia.html',
		[ 'timeout' => 5, 'user-agent' => 'LRMA/1.0' ]
	);

	if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
		set_transient( 'lrma_concerts_feed', [], 20 * MINUTE_IN_SECONDS );
		return [];
	}

	$html = wp_remote_retrieve_body( $response );

	libxml_use_internal_errors( true );
	$dom = new DOMDocument();
	$dom->loadHTML( '<?xml encoding="utf-8"?>' . $html );
	libxml_clear_errors();

	$xpath    = new DOMXPath( $dom );
	$headings = $xpath->query( '//h6' );
	$concerts = [];

	foreach ( $headings as $h6 ) {
		$link_node = $xpath->query( './/a', $h6 )->item( 0 );
		$img_node  = $xpath->query( 'preceding-sibling::a[1]//img', $h6 )->item( 0 );

		if ( ! $link_node ) {
			continue;
		}

		// Extract Unix timestamp from flyer image filename: mini_1771873609--ARTIST.png
		$img_src   = $img_node ? $img_node->getAttribute( 'src' ) : '';
		$timestamp = null;
		if ( preg_match( '/mini_(\d{9,10})--/', $img_src, $m ) ) {
			$timestamp = (int) $m[1];
		}

		// Skip past events
		if ( $timestamp && $timestamp < time() ) {
			continue;
		}

		$href = $link_node->getAttribute( 'href' );
		// Resolve relative href
		if ( str_starts_with( $href, '.' ) ) {
			$href = 'https://en.concerts-metal.com' . ltrim( $href, '.' );
		} elseif ( ! str_starts_with( $href, 'http' ) ) {
			$href = 'https://en.concerts-metal.com/' . ltrim( $href, '/' );
		}

		$thumb = null;
		if ( $img_node ) {
			$src   = $img_node->getAttribute( 'src' );
			$thumb = str_starts_with( $src, 'http' )
				? $src
				: 'https://broadcast.concerts-metal.com/' . ltrim( $src, '/' );
		}

		$concerts[] = [
			'type'  => 'concert',
			'title' => trim( $link_node->textContent ),
			'url'   => $href,
			'thumb' => $thumb,
			'date'  => $timestamp,
		];
	}

	set_transient( 'lrma_concerts_feed', $concerts, 6 * HOUR_IN_SECONDS );
	return $concerts;
}

/**
 * Merged feed: recent WP Koncerti articles + upcoming concert events.
 * Sort: articles from the last 7 days float first, then chronological by date.
 */
function lrma_get_koncerti_feed( int $limit = 8 ): array {
	$posts = get_posts( [
		'category_name'  => 'koncerti',
		'posts_per_page' => 10,
		'date_query'     => [ [ 'after' => '60 days ago' ] ],
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post_status'    => 'publish',
	] );

	$items = array_map( fn( $p ) => [
		'type' => 'article',
		'id'   => $p->ID,
		'date' => get_post_time( 'U', false, $p ),
	], $posts );

	$concerts = lrma_get_upcoming_concerts();
	$merged   = array_merge( $items, $concerts );
	$now      = time();

	usort( $merged, function ( $a, $b ) use ( $now ) {
		$da = $a['date'] ?? PHP_INT_MAX;
		$db = $b['date'] ?? PHP_INT_MAX;

		$a_recent = $a['type'] === 'article' && $da > $now - 7 * DAY_IN_SECONDS;
		$b_recent = $b['type'] === 'article' && $db > $now - 7 * DAY_IN_SECONDS;

		if ( $a_recent && ! $b_recent ) return -1;
		if ( $b_recent && ! $a_recent ) return 1;

		// Articles newest-first, concerts soonest-first — treat both as ascending from now
		if ( $a['type'] === 'article' && $b['type'] === 'article' ) return $db - $da;
		if ( $a['type'] === 'concert' && $b['type'] === 'concert' ) return $da - $db;
		if ( $a['type'] === 'article' ) return $da > $now ? -1 : 1;
		return $db > $now ? 1 : -1;
	} );

	return array_slice( $merged, 0, $limit );
}

/**
 * Render a single card from the Koncerti feed (article or event).
 */
function lrma_render_koncerti_card( array $item ): void {
	if ( $item['type'] === 'article' ) {
		global $post;
		$post = get_post( $item['id'] );
		setup_postdata( $post );
		get_template_part( 'template-parts/card-article', null, [ 'post' => $post, 'context' => 'koncerti' ] );
		wp_reset_postdata();
		return;
	}

	// Concert event card
	$title = esc_html( $item['title'] );
	$url   = esc_url( $item['url'] );
	$date  = $item['date'] ? date_i18n( 'j. F', $item['date'] ) : '';
	$thumb = $item['thumb'] ? esc_url( $item['thumb'] ) : '';
	?>
	<a href="<?php echo $url; ?>" target="_blank" rel="noopener"
	   class="article-card variant-medium article-card--event">
		<?php if ( $thumb ) : ?>
		<div class="article-card__img">
			<img src="<?php echo $thumb; ?>" alt="<?php echo $title; ?>" loading="lazy">
		</div>
		<?php endif; ?>
		<div class="article-card__body">
			<div class="card-tag card-tag--event">Pasākums</div>
			<h3 class="article-card__title"><?php echo $title; ?></h3>
			<div class="card-meta">
				<?php echo $date; ?><?php echo $date ? ' · ' : ''; ?>concerts-metal.com
			</div>
		</div>
	</a>
	<?php
}

// ─── Newsletter AJAX handler ───────────────────────────────────────────────────
add_action( 'wp_ajax_nopriv_lrma_newsletter', 'lrma_handle_newsletter_ajax' );
add_action( 'wp_ajax_lrma_newsletter',        'lrma_handle_newsletter_ajax' );
function lrma_handle_newsletter_ajax() {
	if ( ! check_ajax_referer( 'lrma_newsletter_nonce', 'nonce', false ) ) {
		wp_send_json_error( [ 'message' => 'Drošības kļūda. Lūdzu mēģini vēlreiz.' ], 403 );
	}

	$email = sanitize_email( $_POST['email'] ?? '' );
	if ( ! is_email( $email ) ) {
		wp_send_json_error( [ 'message' => 'Lūdzu ievadi derīgu e-pasta adresi.' ], 400 );
	}

	$subscribers = get_option( 'lrma_newsletter_subscribers', [] );
	if ( in_array( $email, $subscribers, true ) ) {
		wp_send_json_success( [ 'message' => 'Šī e-pasta adrese jau ir reģistrēta.' ] );
	}

	$subscribers[] = $email;
	update_option( 'lrma_newsletter_subscribers', $subscribers, false );

	wp_mail(
		'info@lrma.lv',
		'Jauns biļetena pieteikums',
		"Jauns abonents: $email\n\nKopā abonenti: " . count( $subscribers )
	);

	wp_send_json_success( [ 'message' => 'Paldies! Esat veiksmīgi pieteikušies.' ] );
}
