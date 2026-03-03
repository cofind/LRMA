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
    wp_enqueue_style( 'lrma-style', get_stylesheet_uri(), [ 'lrma-fonts' ], '2.3.0' );

    wp_enqueue_script( 'lrma-main', get_template_directory_uri() . '/assets/js/main.js', [], '2.0.0', true );
    wp_enqueue_script( 'lrma-ajax-nav', get_template_directory_uri() . '/assets/js/ajax-nav.js', [], '1.0.0', true );
    wp_localize_script( 'lrma-main', 'lrmaData', [
        'radioStream' => get_theme_mod( 'radio_url', 'https://rockradio.lv' ),
        'homeUrl'     => home_url( '/' ),
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
